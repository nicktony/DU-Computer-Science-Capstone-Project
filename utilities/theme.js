// Theme change function
function setTheme(theme) {
  if (theme == 'Dark') {
    document.documentElement.style.setProperty('--bg-primary', '#23232e');
    document.documentElement.style.setProperty('--bg-secondary', '#141418');
    document.documentElement.style.setProperty('--bg-third', '#4F4F52');
    document.documentElement.style.setProperty('--text-primary', '#b6b6b6');
    document.documentElement.style.setProperty('--text-secondary', '#ececec');
  } else if (theme == 'Light') {
  	document.documentElement.style.setProperty('--bg-primary', 'white');
    document.documentElement.style.setProperty('--bg-secondary', '#b6b6b6');
    document.documentElement.style.setProperty('--bg-third', '#4F4F52');
    document.documentElement.style.setProperty('--text-primary', '#b6b6b6');
    document.documentElement.style.setProperty('--text-secondary', '#ececec');
  }
}