document.addEventListener("DOMContentLoaded", function () {


    /* HAMBURGER MENU */

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

        navbar.appendChild(hamburger);


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


    /* DARK / LIGHT MODE */

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


    /* NOTIFICATION BANNER */

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


    /* MODAL */

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


    /* REGISTRATION VALIDATION */

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


    /* PRACTICAL 6 - EVENTS */

    const eventsContainer =
        document.getElementById(
            "eventsContainer"
        );


    if (eventsContainer) {

        let eventsData = [];

        let currentEventPage = 1;

        const eventsPerPage = 5;


        const searchInput =
            document.getElementById(
                "eventSearch"
            );

        const categoryFilter =
            document.getElementById(
                "eventCategory"
            );

        const sortSelect =
            document.getElementById(
                "eventSort"
            );

        const loadingMessage =
            document.getElementById(
                "eventLoading"
            );

        const errorMessage =
            document.getElementById(
                "eventError"
            );

        const pagination =
            document.getElementById(
                "eventPagination"
            );


        fetch("events.json")

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            "Unable to load events."
                        );

                    }

                    return response.json();

                }
            )

            .then(
                function (data) {

                    eventsData = data;

                    loadingMessage.style.display =
                        "none";

                    displayEvents();

                }
            )

            .catch(
                function (error) {

                    loadingMessage.style.display =
                        "none";

                    errorMessage.textContent =
                        "Error loading events.";

                    console.error(error);

                }
            );


        function displayEvents() {

            let filteredEvents =
                eventsData.slice();


            const searchText =
                searchInput.value
                    .toLowerCase()
                    .trim();


            if (searchText !== "") {

                filteredEvents =
                    filteredEvents.filter(
                        function (event) {

                            return (
                                event.name
                                    .toLowerCase()
                                    .includes(searchText)
                                ||
                                event.description
                                    .toLowerCase()
                                    .includes(searchText)
                            );

                        }
                    );

            }


            const category =
                categoryFilter.value;


            if (category !== "all") {

                filteredEvents =
                    filteredEvents.filter(
                        function (event) {

                            return event.category ===
                                category;

                        }
                    );

            }


            const sortValue =
                sortSelect.value;


            if (sortValue === "nameAsc") {

                filteredEvents.sort(
                    function (a, b) {

                        return a.name.localeCompare(
                            b.name
                        );

                    }
                );

            }


            if (sortValue === "nameDesc") {

                filteredEvents.sort(
                    function (a, b) {

                        return b.name.localeCompare(
                            a.name
                        );

                    }
                );

            }


            if (sortValue === "dateAsc") {

                filteredEvents.sort(
                    function (a, b) {

                        return new Date(a.date) -
                            new Date(b.date);

                    }
                );

            }


            if (sortValue === "dateDesc") {

                filteredEvents.sort(
                    function (a, b) {

                        return new Date(b.date) -
                            new Date(a.date);

                    }
                );

            }


            const totalPages =
                Math.ceil(
                    filteredEvents.length /
                    eventsPerPage
                );


            if (
                currentEventPage > totalPages &&
                totalPages > 0
            ) {

                currentEventPage =
                    totalPages;

            }


            eventsContainer.innerHTML =
                "";


            if (filteredEvents.length === 0) {

                eventsContainer.innerHTML =
                    "<p>No events found.</p>";

                pagination.innerHTML =
                    "";

                return;

            }


            const startIndex =
                (currentEventPage - 1) *
                eventsPerPage;

            const endIndex =
                startIndex +
                eventsPerPage;


            const pageEvents =
                filteredEvents.slice(
                    startIndex,
                    endIndex
                );


            pageEvents.forEach(
                function (event) {

                    const eventDiv =
                        document.createElement(
                            "div"
                        );

                    eventDiv.className =
                        "event";


                    eventDiv.innerHTML =

                        "<h2>" +
                        event.name +
                        "</h2>" +

                        "<p>" +

                        "<b>Date:</b> " +
                        event.date +

                        "<br><br>" +

                        "<b>Venue:</b> " +
                        event.venue +

                        "<br><br>" +

                        "<b>Category:</b> " +
                        event.category +

                        "<br><br>" +

                        event.description +

                        "</p>";


                    eventsContainer.appendChild(
                        eventDiv
                    );

                }
            );


            createEventPagination(
                totalPages
            );

        }


        function createEventPagination(
            totalPages
        ) {

            pagination.innerHTML =
                "";


            if (totalPages <= 1) {
                return;
            }


            for (
                let i = 1;
                i <= totalPages;
                i++
            ) {

                const button =
                    document.createElement(
                        "button"
                    );

                button.textContent =
                    i;


                if (
                    i === currentEventPage
                ) {

                    button.classList.add(
                        "active"
                    );

                }


                button.addEventListener(
                    "click",
                    function () {

                        currentEventPage =
                            i;

                        displayEvents();

                    }
                );


                pagination.appendChild(
                    button
                );

            }

        }


        searchInput.addEventListener(
            "input",
            function () {

                currentEventPage = 1;

                displayEvents();

            }
        );


        categoryFilter.addEventListener(
            "change",
            function () {

                currentEventPage = 1;

                displayEvents();

            }
        );


        sortSelect.addEventListener(
            "change",
            function () {

                currentEventPage = 1;

                displayEvents();

            }
        );

    }


    /* PRACTICAL 6 - STUDENTS */

    const studentResults =
        document.getElementById(
            "studentResults"
        );


    if (studentResults) {

        let studentsData = [];

        let currentStudentPage = 1;

        const studentsPerPage = 5;


        const searchInput =
            document.getElementById(
                "studentSearch"
            );

        const courseFilter =
            document.getElementById(
                "courseFilter"
            );

        const yearFilter =
            document.getElementById(
                "yearFilter"
            );

        const sortSelect =
            document.getElementById(
                "studentSort"
            );

        const loadingMessage =
            document.getElementById(
                "studentLoading"
            );

        const errorMessage =
            document.getElementById(
                "studentError"
            );

        const pagination =
            document.getElementById(
                "studentPagination"
            );


        fetch("students.json")

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            "Unable to load students."
                        );

                    }

                    return response.json();

                }
            )

            .then(
                function (data) {

                    studentsData = data;

                    loadingMessage.style.display =
                        "none";

                    displayStudents();

                }
            )

            .catch(
                function (error) {

                    loadingMessage.style.display =
                        "none";

                    errorMessage.textContent =
                        "Error loading student results.";

                    console.error(error);

                }
            );


        function displayStudents() {

            let filteredStudents =
                studentsData.slice();


            const searchText =
                searchInput.value
                    .toLowerCase()
                    .trim();


            if (searchText !== "") {

                filteredStudents =
                    filteredStudents.filter(
                        function (student) {

                            return (
                                student.name
                                    .toLowerCase()
                                    .includes(searchText)
                                ||
                                student.email
                                    .toLowerCase()
                                    .includes(searchText)
                            );

                        }
                    );

            }


            const course =
                courseFilter.value;


            if (course !== "all") {

                filteredStudents =
                    filteredStudents.filter(
                        function (student) {

                            return student.course ===
                                course;

                        }
                    );

            }


            const year =
                yearFilter.value;


            if (year !== "all") {

                filteredStudents =
                    filteredStudents.filter(
                        function (student) {

                            return String(
                                student.year
                            ) === year;

                        }
                    );

            }


            const sortValue =
                sortSelect.value;


            if (sortValue === "nameAsc") {

                filteredStudents.sort(
                    function (a, b) {

                        return a.name.localeCompare(
                            b.name
                        );

                    }
                );

            }


            if (sortValue === "nameDesc") {

                filteredStudents.sort(
                    function (a, b) {

                        return b.name.localeCompare(
                            a.name
                        );

                    }
                );

            }


            if (sortValue === "cgpaAsc") {

                filteredStudents.sort(
                    function (a, b) {

                        return a.cgpa - b.cgpa;

                    }
                );

            }


            if (sortValue === "cgpaDesc") {

                filteredStudents.sort(
                    function (a, b) {

                        return b.cgpa - a.cgpa;

                    }
                );

            }


            const totalPages =
                Math.ceil(
                    filteredStudents.length /
                    studentsPerPage
                );


            if (
                currentStudentPage > totalPages &&
                totalPages > 0
            ) {

                currentStudentPage =
                    totalPages;

            }


            studentResults.innerHTML =
                "";


            if (filteredStudents.length === 0) {

                studentResults.innerHTML =
                    "<p>No student records found.</p>";

                pagination.innerHTML =
                    "";

                return;

            }


            const startIndex =
                (currentStudentPage - 1) *
                studentsPerPage;

            const endIndex =
                startIndex +
                studentsPerPage;


            const pageStudents =
                filteredStudents.slice(
                    startIndex,
                    endIndex
                );


            pageStudents.forEach(
                function (student) {

                    const studentDiv =
                        document.createElement(
                            "div"
                        );

                    studentDiv.className =
                        "student-info";


                    studentDiv.innerHTML =

                        "<p>" +
                        "<b>Name :</b> " +
                        student.name +
                        "</p>" +

                        "<p>" +
                        "<b>Course :</b> " +
                        student.course +
                        "</p>" +

                        "<p>" +
                        "<b>Year :</b> " +
                        student.year +
                        "</p>" +

                        "<p>" +
                        "<b>Email :</b> " +
                        student.email +
                        "</p>" +

                        "<p>" +
                        "<b>CGPA :</b> " +
                        student.cgpa +
                        "</p>";


                    studentResults.appendChild(
                        studentDiv
                    );

                }
            );


            createStudentPagination(
                totalPages
            );

        }


        function createStudentPagination(
            totalPages
        ) {

            pagination.innerHTML =
                "";


            if (totalPages <= 1) {
                return;
            }


            for (
                let i = 1;
                i <= totalPages;
                i++
            ) {

                const button =
                    document.createElement(
                        "button"
                    );

                button.textContent =
                    i;


                if (
                    i === currentStudentPage
                ) {

                    button.classList.add(
                        "active"
                    );

                }


                button.addEventListener(
                    "click",
                    function () {

                        currentStudentPage =
                            i;

                        displayStudents();

                    }
                );


                pagination.appendChild(
                    button
                );

            }

        }


        searchInput.addEventListener(
            "input",
            function () {

                currentStudentPage = 1;

                displayStudents();

            }
        );


        courseFilter.addEventListener(
            "change",
            function () {

                currentStudentPage = 1;

                displayStudents();

            }
        );


        yearFilter.addEventListener(
            "change",
            function () {

                currentStudentPage = 1;

                displayStudents();

            }
        );


        sortSelect.addEventListener(
            "change",
            function () {

                currentStudentPage = 1;

                displayStudents();

            }
        );

    }


    /* PRACTICAL 6 - FAQs */

    const faqList =
        document.getElementById(
            "faqList"
        );


    if (faqList) {

        let faqData = [];

        let currentFaqPage = 1;

        const faqsPerPage = 5;


        const searchInput =
            document.getElementById(
                "faqSearch"
            );

        const loadingMessage =
            document.getElementById(
                "faqLoading"
            );

        const errorMessage =
            document.getElementById(
                "faqError"
            );

        const pagination =
            document.getElementById(
                "faqPagination"
            );


        fetch("faqs.json")

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            "Unable to load FAQs."
                        );

                    }

                    return response.json();

                }
            )

            .then(
                function (data) {

                    faqData = data;

                    loadingMessage.style.display =
                        "none";

                    displayFAQs();

                }
            )

            .catch(
                function (error) {

                    loadingMessage.style.display =
                        "none";

                    errorMessage.textContent =
                        "Error loading FAQs.";

                    console.error(error);

                }
            );


        function displayFAQs() {

            let filteredFAQs =
                faqData.slice();


            const searchText =
                searchInput.value
                    .toLowerCase()
                    .trim();


            if (searchText !== "") {

                filteredFAQs =
                    filteredFAQs.filter(
                        function (faq) {

                            return (
                                faq.question
                                    .toLowerCase()
                                    .includes(searchText)
                                ||
                                faq.answer
                                    .toLowerCase()
                                    .includes(searchText)
                            );

                        }
                    );

            }


            const totalPages =
                Math.ceil(
                    filteredFAQs.length /
                    faqsPerPage
                );


            if (
                currentFaqPage > totalPages &&
                totalPages > 0
            ) {

                currentFaqPage =
                    totalPages;

            }


            faqList.innerHTML =
                "";


            if (filteredFAQs.length === 0) {

                faqList.innerHTML =
                    "<p>No FAQs found.</p>";

                pagination.innerHTML =
                    "";

                return;

            }


            const startIndex =
                (currentFaqPage - 1) *
                faqsPerPage;

            const endIndex =
                startIndex +
                faqsPerPage;


            const pageFAQs =
                filteredFAQs.slice(
                    startIndex,
                    endIndex
                );


            pageFAQs.forEach(
                function (faq) {

                    const faqItem =
                        document.createElement(
                            "div"
                        );

                    faqItem.className =
                        "faq-item";


                    const question =
                        document.createElement(
                            "button"
                        );

                    question.className =
                        "faq-question";

                    question.textContent =
                        faq.question;


                    const answer =
                        document.createElement(
                            "div"
                        );

                    answer.className =
                        "faq-answer";

                    answer.textContent =
                        faq.answer;


                    question.addEventListener(
                        "click",
                        function () {

                            faqItem.classList.toggle(
                                "active"
                            );

                        }
                    );


                    faqItem.appendChild(
                        question
                    );

                    faqItem.appendChild(
                        answer
                    );


                    faqList.appendChild(
                        faqItem
                    );

                }
            );


            createFaqPagination(
                totalPages
            );

        }


        function createFaqPagination(
            totalPages
        ) {

            pagination.innerHTML =
                "";


            if (totalPages <= 1) {
                return;
            }


            for (
                let i = 1;
                i <= totalPages;
                i++
            ) {

                const button =
                    document.createElement(
                        "button"
                    );

                button.textContent =
                    i;


                if (
                    i === currentFaqPage
                ) {

                    button.classList.add(
                        "active"
                    );

                }


                button.addEventListener(
                    "click",
                    function () {

                        currentFaqPage =
                            i;

                        displayFAQs();

                    }
                );


                pagination.appendChild(
                    button
                );

            }

        }


        searchInput.addEventListener(
            "input",
            function () {

                currentFaqPage = 1;

                displayFAQs();

            }
        );

    }

});