import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Theme constants
const THEME_KEY = 'theme';
const DEFAULT_THEME = 'light';
const THEMES = ['light', 'dark'] as const;
type Theme = typeof THEMES[number];

// Theme store
export const theme = writable<Theme>(DEFAULT_THEME);

// Get initial theme with type safety
export function getInitialTheme(): Theme {
  if (!browser) return DEFAULT_THEME;
  
  const savedTheme = localStorage.getItem(THEME_KEY);
  if (savedTheme && THEMES.includes(savedTheme as Theme)) {
    return savedTheme as Theme;
  }
  
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

// Apply theme with all necessary DOM updates
function applyThemeToDOM(newTheme: Theme): void {
  if (!browser) return;

  // Update data-bs-theme
  document.documentElement.setAttribute('data-bs-theme', newTheme);
  
  // Update theme classes
  document.documentElement.classList.remove(...THEMES.map(t => `theme-${t}`));
  document.documentElement.classList.add(`theme-${newTheme}`);
  
  // Store in localStorage
  localStorage.setItem(THEME_KEY, newTheme);
}

// Initialize theme system
export function initTheme(): void {
  if (!browser) return;

  // Set initial theme
  const initialTheme = getInitialTheme();
  theme.set(initialTheme);
  applyThemeToDOM(initialTheme);

  // Subscribe to theme store changes
  theme.subscribe((newTheme) => {
    applyThemeToDOM(newTheme);
  });

  // Listen for system theme changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  mediaQuery.addEventListener('change', (e) => {
    if (!localStorage.getItem(THEME_KEY)) {
      const newTheme = e.matches ? 'dark' : 'light';
      theme.set(newTheme as Theme);
    }
  });
}

// Toggle theme
export function toggleTheme(): void {
  theme.update(currentTheme => {
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    return newTheme;
  });
}

// Inline script for immediate theme application
export const themeScript = `
  (function() {
    const THEME_KEY = 'theme';
    const DEFAULT_THEME = 'light';
    const THEMES = ['light', 'dark'];

    function getInitialTheme() {
      const savedTheme = localStorage.getItem(THEME_KEY);
      if (savedTheme && THEMES.includes(savedTheme)) {
        return savedTheme;
      }
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
      document.documentElement.setAttribute('data-bs-theme', theme);
      document.documentElement.classList.remove(...THEMES.map(t => 'theme-' + t));
      document.documentElement.classList.add('theme-' + theme);
    }

    const theme = getInitialTheme();
    applyTheme(theme);
  })();
`; 