        </main>
    </div>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        
        function updateDarkModeIcon() {
            const isDark = document.body.classList.contains('dark-mode');
            darkModeIcon.textContent = isDark ? '☀️' : '🌙';
        }
        
        updateDarkModeIcon();
        
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            updateDarkModeIcon();
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (window.innerWidth <= 768) {
            menuToggle.style.display = 'block';
        }
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
        
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'block';
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('open');
            }
        });

        // Cerrar sidebar al hacer click fuera (móvil)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>
