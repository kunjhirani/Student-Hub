document.addEventListener("DOMContentLoaded", function () {

    /* HAMBURGER MENU */

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

            if (links.classList.contains("mobile-menu")) {

                hamburger.innerHTML = "✕";

                hamburger.setAttribute(
                    "aria-label",
                    "Close Menu"
                );

            } else {

                hamburger.innerHTML = "☰";

                hamburger.setAttribute(
                    "aria-label",
                    "Open Menu"
                );

            }

        });

        const menuLinks = links.querySelectorAll("a");

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


    /* DARK / LIGHT MODE */

    const currentPage =
        window.location.pathname.split("/").pop();

    if (
        currentPage === "about.html" ||
        currentPage === ""
    ) {

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

            navbar.appendChild(themeButton);

            const savedTheme =
                localStorage.getItem("theme");

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


    /* NOTIFICATION */

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


    /* FAQ */

    const faqContainer =
        document.querySelector(
            ".faq-container"
        );

    if (faqContainer) {

        const questions =
            faqContainer.querySelectorAll(
                ".faq-question"
            );

        questions.forEach(function (question) {

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

        });

    }


    /* MODAL */

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

        openButtons.forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    modal.classList.add(
                        "show"
                    );

                }
            );

        });

        closeButtons.forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    modal.classList.remove(
                        "show"
                    );

                }
            );

        });

        modal.addEventListener(
            "click",
            function (event) {

                if (event.target === modal) {

                    modal.classList.remove(
                        "show"
                    );

                }

            }
        );

    }


    /* IMAGE SLIDER */

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

        slides.forEach(function (slide) {

            slide.classList.remove(
                "active"
            );

        });

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


    /* REGISTRATION FORM VALIDATION */

    const registrationForm =
        document.getElementById(
            "registrationForm"
        );

    if (registrationForm) {

        registrationForm.addEventListener(
            "submit",
            function (event) {

                let valid = true;

                const fullName =
                    document.getElementById(
                        "fullName"
                    );

                const email =
                    document.getElementById(
                        "email"
                    );

                const mobile =
                    document.getElementById(
                        "mobile"
                    );

                const password =
                    document.getElementById(
                        "password"
                    );

                const confirmPassword =
                    document.getElementById(
                        "confirmPassword"
                    );

                const course =
                    document.getElementById(
                        "course"
                    );

                const year =
                    document.getElementById(
                        "year"
                    );

                const gender =
                    document.querySelector(
                        'input[name="gender"]:checked'
                    );

                const terms =
                    document.getElementById(
                        "terms"
                    );

                const fullNameError =
                    document.getElementById(
                        "fullNameError"
                    );

                const emailError =
                    document.getElementById(
                        "emailError"
                    );

                const mobileError =
                    document.getElementById(
                        "mobileError"
                    );

                const passwordError =
                    document.getElementById(
                        "passwordError"
                    );

                const confirmPasswordError =
                    document.getElementById(
                        "confirmPasswordError"
                    );

                const courseError =
                    document.getElementById(
                        "courseError"
                    );

                const yearError =
                    document.getElementById(
                        "yearError"
                    );

                const genderError =
                    document.getElementById(
                        "genderError"
                    );

                const termsError =
                    document.getElementById(
                        "termsError"
                    );

                const formMessage =
                    document.getElementById(
                        "formMessage"
                    );


                fullNameError.textContent = "";
                emailError.textContent = "";
                mobileError.textContent = "";
                passwordError.textContent = "";
                confirmPasswordError.textContent = "";
                courseError.textContent = "";
                yearError.textContent = "";
                genderError.textContent = "";
                termsError.textContent = "";

                if (formMessage) {

                    formMessage.textContent = "";

                }


                const namePattern =
                    /^[A-Za-z ]{2,50}$/;

                const emailPattern =
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                const mobilePattern =
                    /^[0-9]{10}$/;

                const passwordPattern =
                    /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/;


                /* Full Name */

                if (
                    fullName.value.trim() === ""
                ) {

                    fullNameError.textContent =
                        "Full name is required.";

                    valid = false;

                } else if (
                    !namePattern.test(
                        fullName.value.trim()
                    )
                ) {

                    fullNameError.textContent =
                        "Enter a valid name.";

                    valid = false;

                }


                /* Email */

                if (
                    email.value.trim() === ""
                ) {

                    emailError.textContent =
                        "Email is required.";

                    valid = false;

                } else if (
                    !emailPattern.test(
                        email.value.trim()
                    )
                ) {

                    emailError.textContent =
                        "Enter a valid email address.";

                    valid = false;

                }


                /* Mobile */

                if (
                    mobile.value.trim() === ""
                ) {

                    mobileError.textContent =
                        "Mobile number is required.";

                    valid = false;

                } else if (
                    !mobilePattern.test(
                        mobile.value.trim()
                    )
                ) {

                    mobileError.textContent =
                        "Mobile number must contain exactly 10 digits.";

                    valid = false;

                }


                /* Password */

                if (
                    password.value === ""
                ) {

                    passwordError.textContent =
                        "Password is required.";

                    valid = false;

                } else if (
                    !passwordPattern.test(
                        password.value
                    )
                ) {

                    passwordError.textContent =
                        "Password must contain 8 characters, uppercase, lowercase, number and special character.";

                    valid = false;

                }


                /* Confirm Password */

                if (
                    confirmPassword.value === ""
                ) {

                    confirmPasswordError.textContent =
                        "Please confirm your password.";

                    valid = false;

                } else if (
                    password.value !==
                    confirmPassword.value
                ) {

                    confirmPasswordError.textContent =
                        "Passwords do not match.";

                    valid = false;

                }


                /* Course */

                if (course.value === "") {

                    courseError.textContent =
                        "Please select your course.";

                    valid = false;

                }


                /* Year */

                if (year.value === "") {

                    yearError.textContent =
                        "Please select your year.";

                    valid = false;

                }


                /* Gender */

                if (!gender) {

                    genderError.textContent =
                        "Please select your gender.";

                    valid = false;

                }


                /* Terms */

                if (!terms.checked) {

                    termsError.textContent =
                        "You must accept the terms and conditions.";

                    valid = false;

                }


                

                if (!valid) {

                    event.preventDefault();

                    if (formMessage) {

                        formMessage.textContent =
                            "Please correct the errors.";

                    }

                }

            }
        );

    }

});