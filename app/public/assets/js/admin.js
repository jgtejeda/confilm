document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stat-value').forEach(function(el) {
        var target = parseInt(el.dataset.value);
        gsap.to({val: 0}, {
            val: target,
            duration: 1.5,
            ease: 'power2.out',
            onUpdate: function() {
                el.textContent = Math.round(this.targets()[0].val);
            }
        });
    });
});
