document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchInput');
    const resultsBox = document.getElementById('liveSearchResults');
    const form = document.getElementById('searchForm');


    const isMobile = () => window.innerWidth < 768;

    input.addEventListener('input', function() {
        const query = this.value.trim();

        console.log(input.value)
        
        if (isMobile() || query.length < 2) {
            resultsBox.classList.add('d-none');
            resultsBox.innerHTML = '';
            return;
        }

        fetch('/config/livesearch.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                resultsBox.innerHTML = '';
                if (data.length === 0) {
                    resultsBox.innerHTML = '<div class="p-2 text-muted">Sonuç bulunamadı.</div>';
                } else {
                    data.forEach(product => {
                        console.log(product);
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center gap-3 p-2 border-bottom live-search-item';
                        item.innerHTML = `
                            <img src="${product.imgPath}" alt="${product.name}" width="40" height="40" class="rounded object-fit-cover">
                            <a href="product-detail.php?id=${product.idproducts}" class="text-dark text-decoration-none flex-grow-1">
                                ${product.name}
                            </a>
                        `;
                        resultsBox.appendChild(item);
                    });
                }
                resultsBox.classList.remove('d-none');
            })
            .catch(err => {
                console.error('Live search error:', err);
                resultsBox.classList.add('d-none');
                resultsBox.innerHTML = '';
            });
    });

    document.addEventListener('click', function(e) {
        if (!form.contains(e.target)) {
            resultsBox.classList.add('d-none');
        }
    });
});