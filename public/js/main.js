/*
 * ============================================================
 * AULAGO — INTERACCIONES DEL HOME
 *
 * En esta etapa JavaScript controla únicamente
 * la experiencia visual del prototipo.
 *
 * Más adelante PHP se encargará de:
 * - usuarios
 * - sesiones
 * - cursos reales
 * - permisos
 * - datos provenientes de MySQL
 * ============================================================
 */


document.addEventListener("DOMContentLoaded", () => {

    initHeader();

    initMobileMenu();

    initCourseCarousel();

    initExperienceExplorer();

});



/* ============================================================
   1. HEADER AL HACER SCROLL
   ============================================================ */

function initHeader() {

    const header =
        document.querySelector("[data-header]");


    if (!header) {
        return;
    }


    const updateHeader = () => {

        header.classList.toggle(
            "is-scrolled",
            window.scrollY > 30
        );

    };


    updateHeader();


    window.addEventListener(
        "scroll",
        updateHeader,
        {
            passive: true
        }
    );

}



/* ============================================================
   2. MENÚ RESPONSIVE
   ============================================================ */

function initMobileMenu() {

    const header =
        document.querySelector("[data-header]");


    const button =
        document.querySelector("[data-menu-button]");


    if (!header || !button) {
        return;
    }


    button.addEventListener("click", () => {

        const isOpen =
            header.classList.toggle("is-menu-open");


        button.setAttribute(
            "aria-expanded",
            String(isOpen)
        );


        document.body.classList.toggle(
            "menu-open",
            isOpen
        );

    });



    /*
     * Escape cierra el menú en caso de estar abierto.
     */

    document.addEventListener("keydown", (event) => {

        if (event.key !== "Escape") {
            return;
        }


        header.classList.remove(
            "is-menu-open"
        );


        button.setAttribute(
            "aria-expanded",
            "false"
        );


        document.body.classList.remove(
            "menu-open"
        );

    });

}



/* ============================================================
   3. CARRUSEL DE CURSOS
   ============================================================ */

function initCourseCarousel() {

    const carousel =
        document.querySelector(
            "[data-course-carousel]"
        );


    if (!carousel) {
        return;
    }


    const cards =
        Array.from(
            carousel.querySelectorAll(
                "[data-course-card]"
            )
        );


    const previousButton =
        carousel.querySelector(
            "[data-course-prev]"
        );


    const nextButton =
        carousel.querySelector(
            "[data-course-next]"
        );


    const track =
        carousel.querySelector(
            ".floating-course-track"
        );


    if (!cards.length) {
        return;
    }



    /*
     * Detectamos qué card comienza seleccionada.
     */

    let activeIndex =
        cards.findIndex((card) => {

            return card.classList.contains(
                "is-selected"
            );

        });


    if (activeIndex < 0) {
        activeIndex = 0;
    }



    /*
     * Sirve para evitar que un swipe termine abriendo
     * accidentalmente la página de un curso.
     */

    let dragDetected = false;



    /* ========================================================
       POSICIÓN RELATIVA DE LAS CARDS

       Ejemplo:

       -2   -1   0   1   2
                 ↑
               activa
       ======================================================== */

    function getRelativePosition(index) {

        const total =
            cards.length;


        const half =
            Math.floor(total / 2);


        let difference =
            index - activeIndex;


        if (difference > half) {

            difference -=
                total;

        }


        if (difference < -half) {

            difference +=
                total;

        }


        return difference;

    }



    /* ========================================================
       ACTUALIZAR INFORMACIÓN DEL CURSO ACTIVO
       ======================================================== */

    function updateCourseInformation() {

        const activeCard =
            cards[activeIndex];


        const category =
            activeCard.dataset.courseCategory
            || "";


        const title =
            activeCard.dataset.courseTitle
            || "";


        const description =
            activeCard.dataset.courseDescription
            || "";


        const instructor =
            activeCard.dataset.courseInstructor
            || "";


        const price =
            activeCard.dataset.coursePrice
            || "";


        const url =
            activeCard.dataset.courseUrl
            || "#";



        const categoryElement =
            carousel.querySelector(
                "[data-selected-category]"
            );


        const titleElement =
            carousel.querySelector(
                "[data-selected-title]"
            );


        const descriptionElement =
            carousel.querySelector(
                "[data-selected-description]"
            );


        const instructorElement =
            carousel.querySelector(
                "[data-selected-instructor]"
            );


        const priceElement =
            carousel.querySelector(
                "[data-selected-price]"
            );


        const currentElement =
            carousel.querySelector(
                "[data-course-current]"
            );


        const linkElement =
            carousel.querySelector(
                "[data-selected-link]"
            );



        if (categoryElement) {

            categoryElement.textContent =
                category;

        }


        if (titleElement) {

            titleElement.textContent =
                title;

        }


        if (descriptionElement) {

            descriptionElement.textContent =
                description;

        }


        if (instructorElement) {

            instructorElement.textContent =
                instructor;

        }


        if (priceElement) {

            priceElement.textContent =
                price;

        }


        if (currentElement) {

            currentElement.textContent =
                String(
                    activeIndex + 1
                ).padStart(
                    2,
                    "0"
                );

        }


        if (linkElement) {

            linkElement.href =
                url;

        }

    }



    /* ========================================================
       REDIBUJAR POSICIONES
       ======================================================== */

    function renderCarousel() {

        cards.forEach((card, index) => {

            const position =
                getRelativePosition(index);


            card.dataset.position =
                String(position);


            card.classList.toggle(
                "is-selected",
                position === 0
            );

        });


        updateCourseInformation();

    }



    /* ========================================================
       CAMBIAR CURSO ACTIVO
       ======================================================== */

    function goTo(index) {

        activeIndex =
            (
                index
                +
                cards.length
            )
            %
            cards.length;


        renderCarousel();

    }



    function goPrevious() {

        goTo(
            activeIndex - 1
        );

    }



    function goNext() {

        goTo(
            activeIndex + 1
        );

    }



    /* ========================================================
       FLECHAS
       ======================================================== */

    previousButton?.addEventListener(
        "click",
        goPrevious
    );


    nextButton?.addEventListener(
        "click",
        goNext
    );



    /* ========================================================
       CLIC SOBRE LAS CARDS

       Card lateral:
       pasa al centro.

       Card central:
       abre detalle del curso.
       ======================================================== */

    cards.forEach((card, index) => {

        const button =
            card.querySelector(
                ".floating-course-card__button"
            );


        button?.addEventListener("click", (event) => {

            /*
             * Si acabamos de hacer swipe,
             * ignoramos este clic.
             */

            if (dragDetected) {

                event.preventDefault();

                dragDetected = false;

                return;

            }


            /*
             * Si no está seleccionada,
             * primero la movemos al centro.
             */

            if (index !== activeIndex) {

                goTo(index);

                return;

            }


            /*
             * Si ya está en el centro,
             * abrimos el detalle.
             */

            const url =
                card.dataset.courseUrl;


            if (
                url
                &&
                url !== "#"
            ) {

                window.location.href =
                    url;

            }

        });

    });



    /* ========================================================
       TECLADO
       ======================================================== */

    document.addEventListener(
        "keydown",
        (event) => {

            /*
             * Evitamos mover el carrusel mientras
             * el usuario escribe en un formulario.
             */

            const element =
                document.activeElement;


            const isTyping =
                element
                &&
                (
                    element.tagName === "INPUT"
                    ||
                    element.tagName === "TEXTAREA"
                    ||
                    element.tagName === "SELECT"
                );


            if (isTyping) {
                return;
            }


            if (event.key === "ArrowLeft") {

                goPrevious();

            }


            if (event.key === "ArrowRight") {

                goNext();

            }

        }
    );



    /* ========================================================
       DESLIZAMIENTO / SWIPE

       Funciona tanto con mouse como con pantalla táctil.
       ======================================================== */

    if (track) {

        let startX = null;

        let currentX = null;



        track.addEventListener(
            "pointerdown",
            (event) => {

                startX =
                    event.clientX;


                currentX =
                    event.clientX;


                dragDetected =
                    false;


                /*
                 * Captura el puntero para que podamos seguir
                 * leyendo el movimiento aunque salga del área.
                 */

                try {

                    track.setPointerCapture(
                        event.pointerId
                    );

                }
                catch (error) {

                    /*
                     * Algunos navegadores pueden no soportarlo.
                     * El carrusel sigue funcionando sin esto.
                     */

                }

            }
        );



        track.addEventListener(
            "pointermove",
            (event) => {

                if (startX === null) {
                    return;
                }


                currentX =
                    event.clientX;


                const distance =
                    currentX - startX;


                if (
                    Math.abs(distance)
                    >
                    10
                ) {

                    dragDetected =
                        true;

                }

            }
        );



        track.addEventListener(
            "pointerup",
            (event) => {

                if (startX === null) {
                    return;
                }


                const endX =
                    event.clientX;


                const distance =
                    endX - startX;



                /*
                 * Más de 55 px se considera un swipe.
                 */

                if (
                    Math.abs(distance)
                    >
                    55
                ) {

                    if (distance > 0) {

                        goPrevious();

                    }
                    else {

                        goNext();

                    }

                }


                startX =
                    null;


                currentX =
                    null;



                /*
                 * Dejamos dragDetected activo unos milisegundos
                 * para evitar que pointerup genere un clic.
                 */

                if (dragDetected) {

                    setTimeout(() => {

                        dragDetected =
                            false;

                    }, 120);

                }

            }
        );



        track.addEventListener(
            "pointercancel",
            () => {

                startX =
                    null;


                currentX =
                    null;


                dragDetected =
                    false;

            }
        );

    }



    /*
     * Estado inicial.
     */

    renderCarousel();

}



/* ============================================================
   4. EXPLORADOR VISUAL

   Mis cursos
   Guardados
   Popular
   Nuevos
   Mi cuenta
   ============================================================ */

function initExperienceExplorer() {

    const options =
        Array.from(
            document.querySelectorAll(
                "[data-experience-option]"
            )
        );


    const previews =
        Array.from(
            document.querySelectorAll(
                "[data-preview-set]"
            )
        );


    const hint =
        document.querySelector(
            ".experience-hint"
        );


    if (
        !options.length
        ||
        !previews.length
    ) {

        return;
    }



    /*
     * Descripción de cada opción.
     */

    const descriptions = {

        "mis-cursos":
            "Consulta tus cursos en progreso, finalizados y pendientes.",

        "guardados":
            "Encuentra los cursos que guardaste para revisarlos después.",

        "popular":
            "Descubre los cursos con mayor interés y participación dentro de AulaGo.",

        "nuevos":
            "Explora los cursos publicados recientemente por los instructores.",

        "mi-cuenta":
            "Accede a tu perfil y administra la información de tu cuenta."

    };



    /*
     * Opción seleccionada inicialmente.
     */

    let activeKey =
        options.find((option) => {

            return option.classList.contains(
                "is-active"
            );

        })?.dataset.experienceOption
        ||
        "popular";



    /* ========================================================
       CAMBIAR OPCIÓN
       ======================================================== */

    function selectOption(key) {

        activeKey =
            key;


        options.forEach((option) => {

            const isActive =
                option.dataset.experienceOption
                ===
                key;


            option.classList.toggle(
                "is-active",
                isActive
            );

        });



        previews.forEach((preview) => {

            const isActive =
                preview.dataset.previewSet
                ===
                key;


            if (isActive) {

                preview.hidden =
                    false;


                /*
                 * requestAnimationFrame permite que el navegador
                 * registre el cambio antes de animar.
                 */

                requestAnimationFrame(() => {

                    preview.classList.add(
                        "is-active"
                    );

                });

            }
            else {

                preview.classList.remove(
                    "is-active"
                );


                preview.hidden =
                    true;

            }

        });



        if (hint) {

            hint.textContent =
                descriptions[key]
                ||
                "";

        }

    }



    /* ========================================================
       NAVEGACIÓN
       ======================================================== */

    function navigateOption(option) {

        const route =
            option.dataset.route;


        const futureRoute =
            option.dataset.futureRoute;



        /*
         * Popular y Nuevos ya tienen rutas provisionales.
         */

        if (
            route
            &&
            route !== "#"
        ) {

            window.location.href =
                route;

            return;

        }



        /*
         * Las demás páginas todavía no existen.
         */

        if (
            futureRoute
            &&
            hint
        ) {

            hint.textContent =
                "Esta sección estará disponible cuando construyamos las pantallas del perfil de usuario.";

        }

    }



    options.forEach((option) => {

        option.addEventListener("click", () => {

            const key =
                option.dataset.experienceOption;


            /*
             * Si pulsamos nuevamente la opción activa,
             * navegamos hacia su página.
             */

            if (key === activeKey) {

                navigateOption(
                    option
                );

                return;

            }


            /*
             * Primer clic:
             * únicamente cambia el preview.
             */

            selectOption(
                key
            );

        });

    });



    selectOption(
        activeKey
    );

}