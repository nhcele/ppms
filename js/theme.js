(function(){
  const storageKey = 'ppms-theme';
  const root = document.documentElement; // <html>

  function applyTheme(theme){
    if(theme === 'dark') {
      root.setAttribute('data-theme','dark');
    } else {
      root.removeAttribute('data-theme');
    }
  }

  function getPreferred(){
    const saved = localStorage.getItem(storageKey);
    if(saved === 'light' || saved === 'dark') return saved;
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    return mq.matches ? 'dark' : 'light';
  }

  function setTheme(theme) {
    // Start transition
    root.style.transition = 'color 300ms ease, background-color 300ms ease';
    
    // Apply theme
    if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      root.setAttribute('data-theme', 'dark');
      localStorage.setItem('ppms-theme', 'dark');
    } else {
      root.setAttribute('data-theme', 'light');
      localStorage.setItem('ppms-theme', 'light');
    }
    
    // Update button state
    updateToggleButton();
    
    // Remove transition after it's done to avoid affecting other transitions
    setTimeout(() => {
      root.style.transition = '';
    }, 300);
  }
  
  function updateToggleButton() {
    const themeToggle = document.getElementById('theme-toggle');
    if (!themeToggle) return;
    
    const isDark = root.getAttribute('data-theme') === 'dark';
    const sunIcon = themeToggle.querySelector('.theme-icon.sun');
    const moonIcon = themeToggle.querySelector('.theme-icon.moon');
    
    if (isDark) {
      sunIcon?.classList.add('d-none');
      moonIcon?.classList.remove('d-none');
      themeToggle.setAttribute('aria-label', 'Switch to light mode');
    } else {
      sunIcon?.classList.remove('d-none');
      moonIcon?.classList.add('d-none');
      themeToggle.setAttribute('aria-label', 'Switch to dark mode');
    }
  }

  // Initialize
  const initial = getPreferred();
  applyTheme(initial);
  window.addEventListener('DOMContentLoaded', function(){
    updateToggleButton();
    const themeToggle = document.getElementById('theme-toggle');
    if(themeToggle){
      themeToggle.addEventListener('click', function(){
        const current = (root.getAttribute('data-theme') === 'dark') ? 'dark' : 'light';
        setTheme(current === 'dark' ? 'light' : 'dark');
      });
    }
  });

  // Listen for system theme changes (only applies if user hasn't set a preference)
  const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
  prefersDarkScheme.addEventListener('change', (e) => {
    if (!localStorage.getItem('ppms-theme')) {
      setTheme(e.matches ? 'dark' : 'light');
    }
  });

  // Make the function available globally in case it needs to be called from elsewhere
  window.setTheme = setTheme;
})();
