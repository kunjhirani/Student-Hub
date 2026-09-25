document.addEventListener("DOMContentLoaded", function () {


    /* =========================================================
       HAMBURGER MENU
       ========================================================= */

    const navbar =
        document.querySelector(".navbar");

    const links =
        document.querySelector(".links");


    if (navbar && links) {

        const hamburger =
            document.createElement("button");

        hamburger.className =
            "hamburger";

        hamburger.innerHTML =
            "☰";

        hamburger.setAttribute(
            "aria-label",
            "Open Menu"
        );

        navbar.appendChild(
            hamburger
        );


        hamburger.addEventListener(
            "click",
            function () {

                links.classList.toggle(
                    "mobile-menu"
                );


                if (
                    links.classList.contains(
                        "mobile-menu"
                    )
                ) {

                    hamburger.innerHTML =
                        "✕";

                    hamburger.setAttribute(
                        "aria-label",
                        "Close Menu"
                    );

                } else {

                    hamburger.innerHTML =
                        "☰";

                    hamburger.setAttribute(
                        "aria-label",
                        "Open Menu"
                    );

                }

            }
        );


        const menuLinks =
            links.querySelectorAll("a");


        menuLinks.forEach(
            function (link) {

                link.addEventListener(
                    "click",
                    function () {

                        links.classList.remove(
                            "mobile-menu"
                        );

                        hamburger.innerHTML =
                            "☰";

                        hamburger.setAttribute(
                            "aria-label",
                            "Open Menu"
                        );

                    }
                );

            }
        );

    }



    /* =========================================================
       DARK / LIGHT MODE
       ABOUT PAGE ONLY
       ========================================================= */

    const currentPage =
        window.location.pathname
            .split("/")
            .pop();


    if (currentPage === "about.html") {

        if (navbar) {

            const themeButton =
                document.createElement("button");

            themeButton.className =
                "theme-button";

            themeButton.innerHTML =
                "🌙 Dark";

            themeButton.setAttribute(
                "aria-label",
                "Toggle Dark and Light Mode"
            );

            navbar.appendChild(
                themeButton
            );


            const savedTheme =
                localStorage.getItem(
                    "theme"
                );


            if (savedTheme === "dark") {

                document.body.classList.add(
                    "dark-mode"
                );

                themeButton.innerHTML =
                    "☀️ Light";

            }


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
        document.querySelector(
            ".modal"
        );

    const openButtons =
        document.querySelectorAll(
            ".open-modal"
        );

    const closeButtons =
        document.querySelectorAll(
            ".modal-close"
        );


    if (modal) {

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

        showSlide(currentSlide);


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


        if (previousButton) {

            previousButton.addEventListener(
                "click",
                function () {

                    currentSlide--;


                    if (currentSlide < 0) {

                        currentSlide =
                            slides.length - 1;

                    }


                    showSlide(
                        currentSlide
                    );

                }
            );

        }


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



    /* =========================================================
       PRACTICAL 5
       SIMPLE STUDENT REGISTRATION VALIDATION
       ========================================================= */

    const registrationForm =
        document.getElementById(
            "registrationForm"
        );


    if (registrationForm) {


        const nameInput =
            document.getElementById(
                "fullName"
            );

        const emailInput =
            document.getElementById(
                "email"
            );

        const mobileInput =
            document.getElementById(
                "mobile"
            );

        const passwordInput =
            document.getElementById(
                "password"
            );

        const confirmPasswordInput =
            document.getElementById(
                "confirmPassword"
            );

        const courseInput =
            document.getElementById(
                "course"
            );

        const yearInput =
            document.getElementById(
                "year"
            );

        const termsInput =
            document.getElementById(
                "terms"
            );


        registrationForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                let valid = true;


                /* NAME */

                const nameRegex =
                    /^[A-Za-z ]+$/;


                if (
                    nameInput.value.trim() === ""
                ) {

                    document.getElementById(
                        "fullNameError"
                    ).textContent =
                        "Name is required.";

                    valid = false;

                } else if (
                    !nameRegex.test(
                        nameInput.value.trim()
                    )
                ) {

                    document.getElementById(
                        "fullNameError"
                    ).textContent =
                        "Name should contain only letters.";

                    valid = false;

                } else {

                    document.getElementById(
                        "fullNameError"
                    ).textContent =
                        "";

                }



                /* EMAIL */

                const emailRegex =
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


                if (
                    emailInput.value.trim() === ""
                ) {

                    document.getElementById(
                        "emailError"
                    ).textContent =
                        "Email is required.";

                    valid = false;

                } else if (
                    !emailRegex.test(
                        emailInput.value.trim()
                    )
                ) {

                    document.getElementById(
                        "emailError"
                    ).textContent =
                        "Enter a valid email address.";

                    valid = false;

                } else {

                    document.getElementById(
                        "emailError"
                    ).textContent =
                        "";

                }



                /* MOBILE */

                const mobileRegex =
                    /^[0-9]{10}$/;


                if (
                    mobileInput.value.trim() === ""
                ) {

                    document.getElementById(
                        "mobileError"
                    ).textContent =
                        "Mobile number is required.";

                    valid = false;

                } else if (
                    !mobileRegex.test(
                        mobileInput.value.trim()
                    )
                ) {

                    document.getElementById(
                        "mobileError"
                    ).textContent =
                        "Mobile number must contain 10 digits.";

                    valid = false;

                } else {

                    document.getElementById(
                        "mobileError"
                    ).textContent =
                        "";

                }



                /* PASSWORD */

                const passwordRegex =
                    /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/;


                if (
                    passwordInput.value === ""
                ) {

                    document.getElementById(
                        "passwordError"
                    ).textContent =
                        "Password is required.";

                    valid = false;

                } else if (
                    !passwordRegex.test(
                        passwordInput.value
                    )
                ) {

                    document.getElementById(
                        "passwordError"
                    ).textContent =
                        "Password must contain 8 characters, uppercase, lowercase, number and special character.";

                    valid = false;

                } else {

                    document.getElementById(
                        "passwordError"
                    ).textContent =
                        "";

                }



                /* CONFIRM PASSWORD */

                if (
                    confirmPasswordInput.value === ""
                ) {

                    document.getElementById(
                        "confirmPasswordError"
                    ).textContent =
                        "Please confirm your password.";

                    valid = false;

                } else if (
                    confirmPasswordInput.value !==
                    passwordInput.value
                ) {

                    document.getElementById(
                        "confirmPasswordError"
                    ).textContent =
                        "Passwords do not match.";

                    valid = false;

                } else {

                    document.getElementById(
                        "confirmPasswordError"
                    ).textContent =
                        "";

                }



                /* COURSE */

                if (
                    courseInput.value === ""
                ) {

                    document.getElementById(
                        "courseError"
                    ).textContent =
                        "Please select your course.";

                    valid = false;

                } else {

                    document.getElementById(
                        "courseError"
                    ).textContent =
                        "";

                }



                /* YEAR */

                if (
                    yearInput.value === ""
                ) {

                    document.getElementById(
                        "yearError"
                    ).textContent =
                        "Please select your year.";

                    valid = false;

                } else {

                    document.getElementById(
                        "yearError"
                    ).textContent =
                        "";

                }



                /* GENDER */

                const gender =
                    document.querySelector(
                        'input[name="gender"]:checked'
                    );


                if (!gender) {

                    document.getElementById(
                        "genderError"
                    ).textContent =
                        "Please select your gender.";

                    valid = false;

                } else {

                    document.getElementById(
                        "genderError"
                    ).textContent =
                        "";

                }



                /* TERMS */

                if (
                    !termsInput.checked
                ) {

                    document.getElementById(
                        "termsError"
                    ).textContent =
                        "You must accept the terms and conditions.";

                    valid = false;

                } else {

                    document.getElementById(
                        "termsError"
                    ).textContent =
                        "";

                }



                /* FINAL MESSAGE */

                const formMessage =
                    document.getElementById(
                        "formMessage"
                    );


                if (valid) {

                    formMessage.textContent =
                        "Registration successful!";

                    formMessage.className =
                        "form-message success-message";

                } else {

                    formMessage.textContent =
                        "Please correct the errors.";

                    formMessage.className =
                        "form-message error-message";

                }

            }
        );

    }

});