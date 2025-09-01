# Template: Customização de Cores Filament v3

## Problema Comum
Trocar cores padrão do Filament (rosa/magenta) por cores customizadas mantendo funcionalidade.

## Solução Comprovada

### 1. CSS Principal
```css
/* Cores primárias */
:root {
    --primary-500: 0, 255, 0 !important;
    --primary-600: 0, 204, 0 !important;
}

/* Elementos principais */
.fi-btn-primary, 
.fi-btn--color-primary,
[data-color="primary"] {
    background-color: #00ff00 !important;
    border-color: #00ff00 !important;
}

/* Sidebar específica - textos brancos */
.fi-sidebar-group-label.flex-1.text-sm.font-medium.leading-6.text-gray-500.dark\\:text-gray-400 {
    color: rgb(255, 255, 255) !important;
}

/* Sidebar - ícones verdes */
.fi-sidebar-group-collapse-button svg,
.fi-sidebar-item-button svg {
    color: rgb(0, 255, 0) !important;
    fill: rgb(0, 255, 0) !important;
}
```

### 2. JavaScript para Especificidade Máxima
```javascript
document.querySelectorAll('.fi-sidebar nav ul li a span').forEach((el) => {
  if (el.textContent.trim() !== '' && !el.closest('.fi-sidebar-group-label')) {
    el.style.setProperty('color', 'rgb(255, 255, 255)', 'important');
  }
});
```

### 3. Provider Configuration
```php
// FilamentServiceProvider.php
Filament::serving(function () {
    Filament::registerStylesheet(asset('css/custom-filament-theme.css'));
});

// AdminPanelProvider.php
->darkMode(true)
->colors(['primary' => Color::Lime])
```

## Armadilhas Comuns
- ❌ CSS universal `* { color: inherit !important; }` quebra widgets
- ❌ Especificidade insuficiente não sobrescreve classes Filament
- ❌ Esquecer JavaScript para sidebar em casos extremos

## Resultado Esperado
- ✅ Dashboard funcional mantido
- ✅ Cores customizadas aplicadas
- ✅ Sidebar com textos brancos e ícones verdes