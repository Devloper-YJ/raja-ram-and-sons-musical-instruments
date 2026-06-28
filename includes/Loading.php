<style>
/* 1. Ye code #preloader ko screen par fix karega */
#preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #ffffff; /* Page ka background color */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999999; /* Sabse upar dikhega */
}

/* 2. Aapka original loader styling */
.loader {
  position: relative;
  width: 180px; /* Thoda adjust kiya */
  height: 100px;
}

.loader div {
  position: absolute;
  width: 10px;
  height: 30px;
  background-color: #ff6a00;
  border-radius: 5px;
  animation: loader_51899 1.5s ease-in-out infinite;
}

.bar1 { left: 0px; animation-delay: 0s; }
.bar2 { left: 20px; animation-delay: 0.15s; }
.bar3 { left: 40px; animation-delay: 0.3s; }
.bar4 { left: 60px; animation-delay: 0.45s; }
.bar5 { left: 80px; animation-delay: 0.6s; }
.bar6 { left: 100px; animation-delay: 0.75s; }
.bar7 { left: 120px; animation-delay: 0.9s; }
.bar8 { left: 140px; animation-delay: 1.05s; }
.bar9 { left: 160px; animation-delay: 1.2s; }

@keyframes loader_51899 {
  0%, 100% { height: 30px; transform: translate(0, 0); }
  50% { height: 70px; transform: translate(0, 35px); }
}
</style>

<div id="preloader">
    <div class="loader">
        <?php for ($i = 1; $i <= 9; $i++): ?>
            <div class="bar<?php echo $i; ?>"></div>
        <?php endfor; ?>
    </div>
</div>
