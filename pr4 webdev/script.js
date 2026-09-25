document.addEventListener("DOMContentLoaded", function () {


    /* =========================================================
       HAMBURGER MENU
       ========================================================= */

    const navbar = document.querySelector(".navbar");
    const links = document.querySelector(".links");

    if (navbar && links) {

        const hamburger = document.createElement("button");

        hamburger.className = "hamburger";

        hamburger.innerHTML = "☰";

        hamburger.setAttribute("aria-label", "Open Menu");

        navbar.appendChild(hamburger);


        hamburger.addEventListener("click", function () {

            links.classList.toggle("mobile-menu");


            
        });


        /* Close menu when a link is clicked */

        const menuLinks =
            links.querySelectorAll("a");

        menuLinks.forEach(function (link) {

            link.addEventListener("click", function () {

                links.classList.remove("mobile-menu");

                hamburger.innerHTML = "☰";

                hamburger.setAttribute(
                    "aria-label",
                    "Open Menu"
                );

            });

        });

    }



    /*DARK / LIGHT MODE*/

 

    const currentPage =
        window.location.pathname.split("/").pop();


    if (
        currentPage === "about.html" ||
        currentPage === ""
    ) {

        if (navbar) {

            const themeButton =
                document.createElement("button");

            themeButton.className = "theme-button";

            themeButton.innerHTML = "🌙 Dark";

            themeButton.setAttribute(
                "aria-label",
                "Toggle Dark and Light Mode"
            );

            navbar.appendChild(themeButton);


            /* Check previously saved theme */

            const savedTheme =
                localStorage.getItem("theme");


            if (savedTheme === "dark") {

                document.body.classList.add("dark-mode");

                themeButton.innerHTML = "☀️ Light";

            }


            /* Theme button click */

            themeButton.addEventListener(
                "click",
                function () {

                    document.body.classList.toggle(
                        "dark-mode"
                    );


                    if (
                        document.body.classList.contains(
                            "dark-mode"
                        )
                    ) {

                        themeButton.innerHTML =
                            "☀️ Light";

                        localStorage.setItem(
                            "theme",
                            "dark"
                        );

                    } else {

                        themeButton.innerHTML =
                            "🌙 Dark";

                        localStorage.setItem(
                            "theme",
                            "light"
                        );

                    }

                }
            );

        }

    }



    /* =========================================================
       NOTIFICATION BANNER
       ========================================================= */

    const notification =
        document.createElement("div");

    notification.className =
        "notification-banner";

    notification.innerHTML =
        '📢 Welcome to Student Hub! Check the latest courses, events and academic updates. ' +
        '<button id="closeNotification" aria-label="Close Notification">×</button>';


    document.body.insertBefore(
        notification,
        document.body.firstChild
    );


    const closeNotification =
        document.getElementById(
            "closeNotification"
        );


    if (closeNotification) {

        closeNotification.addEventListener(
            "click",
            function () {

                notification.style.display =
                    "none";

            }
        );

    }



    /* =========================================================
       FAQ
       ========================================================= */

    const faqContainer =
        document.querySelector(
            ".faq-container"
        );


    if (faqContainer) {

        const questions =
            faqContainer.querySelectorAll(
                ".faq-question"
            );


        questions.forEach(
            function (question) {

                question.addEventListener(
                    "click",
                    function () {

                        const item =
                            question.parentElement;

                        item.classList.toggle(
                            "active"
                        );

                    }
                );

            }
        );

    }



    /* =========================================================
       MODAL
       ========================================================= */

    const modal =
        document.querySelector(".modal");


    const openButtons =
        document.querySelectorAll(
            ".open-modal"
        );


    const closeButtons =
        document.querySelectorAll(
            ".modal-close"
        );


    if (modal) {


        /* Open Modal */

        openButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        modal.classList.add(
                            "show"
                        );

                    }
                );

            }
        );


        /* Close Modal */

        closeButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        modal.classList.remove(
                            "show"
                        );

                    }
                );

            }
        );


        /* Close Modal by clicking outside */

        modal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target === modal
                ) {

                    modal.classList.remove(
                        "show"
                    );

                }

            }
        );

    }



    /* =========================================================
       IMAGE SLIDER
       ========================================================= */

    const slides =
        document.querySelectorAll(
            ".slide"
        );


    const previousButton =
        document.querySelector(
            ".previous-slide"
        );


    const nextButton =
        document.querySelector(
            ".next-slide"
        );


    let currentSlide = 0;


    /* Show selected slide */

    function showSlide(index) {

        if (slides.length === 0) {

            return;

        }


        slides.forEach(
            function (slide) {

                slide.classList.remove(
                    "active"
                );

            }
        );


        slides[index].classList.add(
            "active"
        );

    }



    if (slides.length > 0) {


        /* Show first slide */

        showSlide(currentSlide);


        /* Next button */

        if (nextButton) {

            nextButton.addEventListener(
                "click",
                function () {

                    currentSlide++;


                    if (
                        currentSlide >=
                        slides.length
                    ) {

                        currentSlide = 0;

                    }


                    showSlide(
                        currentSlide
                    );

                }
            );

        }


        /* Previous button */

        if (previousButton) {

            previousButton.addEventListener(
                "click",
                function () {

                    currentSlide--;


                    if (
                        currentSlide < 0
                    ) {

                        currentSlide =
                            slides.length - 1;

                    }


                    showSlide(
                        currentSlide
                    );

                }
            );

        }


        /* Automatic Slider */

        setInterval(
            function () {

                currentSlide++;


                if (
                    currentSlide >=
                    slides.length
                ) {

                    currentSlide = 0;

                }


                showSlide(
                    currentSlide
                );

            },
            5000
        );

    }

});
