<div id="loader-overlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 12, 29, 0.3); backdrop-filter:blur(10px); display:none; justify-content:center; align-items:center; z-index:9999999;">
    <div style="display:flex; align-items:center; gap:12px;">
        <div class="v-string" style="width:6px; height:40px; background:#ff6a00; border-radius:3px; animation:vibe 0.5s infinite alternate;"></div>
        <div class="v-string" style="width:6px; height:40px; background:#ff6a00; border-radius:3px; animation:vibe 0.5s infinite alternate 0.1s;"></div>
        <div class="v-string" style="width:6px; height:60px; background:#ff6a00; border-radius:3px; animation:vibe 0.5s infinite alternate 0.2s;"></div>
        <div class="v-string" style="width:6px; height:40px; background:#ff6a00; border-radius:3px; animation:vibe 0.5s infinite alternate 0.3s;"></div>
        <div class="v-string" style="width:6px; height:40px; background:#ff6a00; border-radius:3px; animation:vibe 0.5s infinite alternate 0.4s;"></div>
    </div>
</div>

<style>
    @keyframes vibe {
        0% { transform: scaleY(0.5); opacity: 0.3; }
        100% { transform: scaleY(1.8); opacity: 1; }
    }
</style>

<script>
    // 1. Click hote hi dikhao
    document.addEventListener("click", function(e) {
        let link = e.target.closest('a');
        if (link && link.href && !link.target && !link.href.startsWith('#')) {
            document.getElementById('loader-overlay').style.display = 'flex';
        }
    });

    // 2. Load hote hi hatao
    window.addEventListener("load", function() {
        document.getElementById('loader-overlay').style.display = 'none';
    });
</script>
