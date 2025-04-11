<div class="fixed-background"></div>

<style>
.fixed-background {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: -1;
    background: linear-gradient(135deg, #487DA9 0%, #16213e 100%);
}

.content-wrapper {
    position: relative;
    z-index: 1;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
</style>
