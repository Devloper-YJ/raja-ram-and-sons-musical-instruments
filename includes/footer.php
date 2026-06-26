<?php
// footer.php
?>
<footer class="bg-brand-dark text-white pt-12 pb-6 border-t-4 border-brand-gold mt-auto relative z-50">
    <div class="max-w-[1400px] mx-auto px-6">
        
        <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-8 mb-8 text-center md:text-left">
            <div>
                <h1 class="text-2xl font-black font-serif tracking-tight mb-2">RajaRam <span class="text-brand-gold italic font-normal">&</span> Sons</h1>
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-4">Preserving Heritage & Melodies Since 1951</p>
            </div>
            
            <div>
                <h3 class="text-brand-gold font-bold mb-3 uppercase tracking-wider text-sm">Contact Us</h3>
                <p class="text-sm text-gray-300 mb-1">Uplipad Road, Opp. Hatkeshwar Temple</p>
                <p class="text-sm text-gray-300 mb-3">Bhuj, Kutch, Gujarat - 370001</p>
                <p class="text-sm text-gray-300 font-bold flex items-center justify-center md:justify-start gap-2">
                     <span class="text-brand-gold">📞</span> +91 98799 57792   
                    <span class="text-brand-gold">📞</span> +91 98255 80615
                </p>
            </div>
        </div>

        <div class="text-gray-400 text-xs border-t border-white/10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> RajaRam & Sons Music Mall. All Rights Reserved.</p>
            
            <p class="font-medium tracking-wide">
                Developed by <span class="text-brand-gold font-bold">Yuvraj Chudasama</span> & <span class="text-brand-gold font-bold">Meet Sonara</span> (74339 65406)
            </p>
        </div>
        
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('frontSearchInput');
        const searchCategory = document.getElementById('frontSearchCategory');
        const searchResults = document.getElementById('frontSearchResults');

        if(searchInput && searchResults) {
            searchInput.addEventListener('keyup', fetchResults);
            if(searchCategory) searchCategory.addEventListener('change', fetchResults);

            function fetchResults() {
                let query = searchInput.value.trim();
                let category = searchCategory ? searchCategory.value : 'all';
                
                if (query.length >= 2) {
                    fetch('/customer/front_ajax_search.php?q=' + encodeURIComponent(query) + '&category=' + encodeURIComponent(category))
                        .then(response => response.text())
                        .then(data => {
                            searchResults.innerHTML = data;
                            searchResults.classList.remove('hidden');
                        });
                } else {
                    searchResults.innerHTML = '';
                    searchResults.classList.add('hidden');
                }
            }

            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !searchResults.contains(event.target) && (!searchCategory || !searchCategory.contains(event.target))) {
                    searchResults.classList.add('hidden');
                }
            });
        }
    });
</script>