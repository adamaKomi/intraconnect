document.addEventListener('DOMContentLoaded', (event) => {
    const searchInput = document.getElementById('searchQuery');
    const searchButton = document.getElementById('searchButton');
    const occurenceDisplay = document.querySelector('.nombreOccur');
    let currentIndex = -1;
    let results = [];

    const clearHighlights = () => {
        document.querySelectorAll('.highlight').forEach(element => {
            element.classList.remove('highlight');
        });
        document.querySelectorAll('.current').forEach(element => {
            element.classList.remove('current');
        });
    };

    const removeAccents = (str) => {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
    };

    const updateOccurenceDisplay = () => {
        occurenceDisplay.textContent = `${currentIndex + 1}/${results.length}`;
    };

    const highlightResults = () => {
        clearHighlights();
        const searchValue = removeAccents(searchInput.value.trim().toLowerCase());
        if (!searchValue) {
            results = [];
            currentIndex = -1;
            updateOccurenceDisplay();
            return;
        }

        const allTextElements = document.querySelectorAll('body *:not(script):not(style):not(meta):not(link):not(title):not(head)');

        results = [];
        allTextElements.forEach(element => {
            if (element.childElementCount === 0) {
                const elementText = removeAccents(element.textContent.trim().toLowerCase());
                if (elementText.includes(searchValue)) {
                    element.classList.add('highlight');
                    results.push(element);
                }
            }
        });

        if (results.length > 0) {
            currentIndex = 0;
            results[currentIndex].classList.add('current');
            results[currentIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        } else {
            currentIndex = -1;
        }
        updateOccurenceDisplay();
    };

    const navigateResults = (direction) => {
        if (results.length) {
            results[currentIndex].classList.remove('current');
            currentIndex += direction;
            if (currentIndex >= results.length) {
                currentIndex = 0;
            }
            if (currentIndex < 0) {
                currentIndex = results.length - 1;
            }
            results[currentIndex].classList.add('current');
            results[currentIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
        updateOccurenceDisplay();
    };

    searchButton.addEventListener('click', (e) => {
        e.preventDefault();
        highlightResults();
    });

    searchInput.addEventListener('input', highlightResults);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (results.length > 0) {
                navigateResults(1);
            }
        }
    });
});
