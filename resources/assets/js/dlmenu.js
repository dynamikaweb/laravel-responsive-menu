/**
 * dlmenu.js — versão sem jQuery do jquery.dlmenu.js v1.0.1 (Codrops, MIT).
 * Uso: new DLMenu(document.getElementById('dl-menu'), { backLabel: 'Voltar' })
 */
(function (window, document) {
    'use strict';

    const PADRAO = {
        animationClasses: { classin: 'dl-animate-in-1', classout: 'dl-animate-out-1' },
        onLevelClick: function () {},
        onLinkClick: function () {},
        backLabel: 'Voltar',
        useActiveItemAsBackLabel: false,
        useActiveItemAsLink: false,
        resetOnClose: true,
    };

    function filhos(el, seletor) {
        return Array.from(el.children).filter((filho) => filho.matches(seletor));
    }

    // Executa fn no fim da animação/transição do elemento (ou após o tempo limite, se ela não ocorrer).
    function aoTerminar(el, evento, fn, limite) {
        let feito = false;
        const finalizar = () => {
            if (feito) return;
            feito = true;
            el.removeEventListener(evento, finalizar);
            clearTimeout(temporizador);
            fn();
        };
        el.addEventListener(evento, finalizar);
        const temporizador = setTimeout(finalizar, limite || 800);
    }

    class DLMenu {
        constructor(el, opcoes = {}) {
            this.el = el;
            this.opcoes = {
                ...PADRAO,
                ...opcoes,
                animationClasses: { ...PADRAO.animationClasses, ...(opcoes.animationClasses || {}) },
            };
            this.aberto = false;
            this.trigger = filhos(el, '.dl-trigger')[0];
            this.menu = filhos(el, 'ul.dl-menu')[0];
            if (!this.trigger || !this.menu) return;

            this.itens = Array.from(this.menu.querySelectorAll('li:not(.dl-back)'));
            el.querySelectorAll('ul.dl-submenu').forEach((ul) => {
                const voltar = document.createElement('li');
                voltar.className = 'dl-back';
                voltar.innerHTML = '<a href="#"></a>';
                voltar.firstChild.textContent = this.opcoes.backLabel;
                ul.appendChild(voltar);
            });
            this.voltar = Array.from(this.menu.querySelectorAll('li.dl-back'));

            if (this.opcoes.useActiveItemAsBackLabel) {
                this.voltar.forEach((li) => {
                    const pai = li.parentElement.closest('li');
                    const link = pai && pai.querySelector('a');
                    if (link) li.querySelector('a').textContent = link.textContent;
                });
            }

            if (this.opcoes.useActiveItemAsLink) {
                el.querySelectorAll('ul.dl-submenu').forEach((ul) => {
                    const link = ul.parentElement.closest('li:not(.dl-back)')?.querySelector('a');
                    if (!link) return;
                    const li = document.createElement('li');
                    li.className = 'dl-parent';
                    li.innerHTML = '<a></a>';
                    li.firstChild.href = link.getAttribute('href') || '#';
                    li.firstChild.textContent = link.textContent;
                    ul.insertBefore(li, ul.firstChild);
                });
            }

            this.fecharAoClicarFora = () => this.closeMenu();
            this.iniciarEventos();
            el.dlmenu = this;
        }

        iniciarEventos() {
            this.trigger.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (this.aberto) {
                    this._closeMenu();
                } else {
                    this._openMenu();
                }
            });

            this.itens.forEach((item) => {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const submenu = filhos(item, 'ul.dl-submenu')[0];

                    if (submenu && !item.classList.contains('dl-subviewopen')) {
                        e.preventDefault();
                        this.abrirNivel(item, submenu);
                    } else {
                        this.opcoes.onLinkClick(item, e);
                    }
                });
            });

            this.voltar.forEach((li) => {
                li.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.voltarNivel(li);
                });
            });
        }

        abrirNivel(item, submenu) {
            const { classin, classout } = this.opcoes.animationClasses;
            const flyin = submenu.cloneNode(true);
            flyin.style.opacity = 0;
            this.menu.after(flyin);

            setTimeout(() => {
                flyin.classList.add(classin);
                this.menu.classList.add(classout);
                aoTerminar(this.menu, 'animationend', () => {
                    this.menu.classList.remove(classout);
                    this.menu.classList.add('dl-subview');
                    item.classList.add('dl-subviewopen');
                    const anterior = item.parentElement.closest('.dl-subviewopen');
                    if (anterior) {
                        anterior.classList.remove('dl-subviewopen');
                        anterior.classList.add('dl-subview');
                    }
                    flyin.remove();
                });
                const link = filhos(item, 'a')[0];
                this.opcoes.onLevelClick(item, link ? link.textContent : '');
            });
        }

        voltarNivel(li) {
            const { classin, classout } = this.opcoes.animationClasses;
            const submenu = li.closest('ul.dl-submenu');
            const item = submenu.parentElement;
            const flyin = submenu.cloneNode(true);
            this.menu.after(flyin);

            setTimeout(() => {
                flyin.classList.add(classout);
                this.menu.classList.add(classin);
                aoTerminar(this.menu, 'animationend', () => {
                    this.menu.classList.remove(classin);
                    flyin.remove();
                });

                item.classList.remove('dl-subviewopen');
                const subview = li.parentElement.closest('.dl-subview');
                if (subview) {
                    if (subview.matches('li')) subview.classList.add('dl-subviewopen');
                    subview.classList.remove('dl-subview');
                }
            });
        }

        openMenu() {
            if (!this.aberto) this._openMenu();
        }

        closeMenu() {
            if (this.aberto) this._closeMenu();
        }

        _openMenu() {
            document.addEventListener('click', this.fecharAoClicarFora);
            this.menu.classList.add('dl-menuopen', 'dl-menu-toggle');
            aoTerminar(this.menu, 'transitionend', () => this.menu.classList.remove('dl-menu-toggle'));
            this.trigger.classList.add('dl-active');
            this.aberto = true;
        }

        _closeMenu() {
            document.removeEventListener('click', this.fecharAoClicarFora);
            this.menu.classList.remove('dl-menuopen');
            this.menu.classList.add('dl-menu-toggle');
            this.trigger.classList.remove('dl-active');
            aoTerminar(this.menu, 'transitionend', () => {
                if (this.opcoes.resetOnClose) this._resetMenu();
            });
            this.aberto = false;
        }

        _resetMenu() {
            this.menu.classList.remove('dl-subview');
            this.itens.forEach((item) => item.classList.remove('dl-subview', 'dl-subviewopen'));
        }
    }

    window.DLMenu = DLMenu;

    // Compatibilidade com projetos que ainda chamam $('#dl-menu').dlmenu(opcoes) / .dlmenu('closeMenu').
    if (window.jQuery) {
        window.jQuery.fn.dlmenu = function (opcoes, ...args) {
            return this.each(function () {
                if (typeof opcoes === 'string') {
                    if (this.dlmenu && typeof this.dlmenu[opcoes] === 'function' && opcoes.charAt(0) !== '_') {
                        this.dlmenu[opcoes](...args);
                    }
                } else if (!this.dlmenu) {
                    new DLMenu(this, opcoes);
                }
            });
        };
    }
})(window, document);
