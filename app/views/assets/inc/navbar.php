<!-- Sidebar professionnelle -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/" class="sidebar-brand">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="brand-text">BNGRC</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <!-- Tableau de bord -->
        <div class="nav-item">
            <a class="nav-link" href="/tableau-bord">
                <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span class="link-text">Tableau de bord</span>
            </a>
        </div>

        <!-- Récapitulation -->
        <div class="nav-item">
            <a class="nav-link" href="/recapitulation">
                <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span class="link-text">Récapitulation</span>
            </a>
        </div>

        <!-- Besoins avec sous-menu -->
        <div class="nav-item has-submenu">
            <div class="nav-link-wrapper">
                <a class="nav-link" href="/besoins">
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span class="link-text">Besoins</span>
                </a>
                <button class="submenu-toggle" onclick="toggleSubmenu(event, 'besoins-submenu')">
                    <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="submenu" id="besoins-submenu">
                <a href="/besoins">Liste des besoins</a>
                <a href="/besoin/create">Ajouter un besoin</a>
                <a href="/besoins/non-satisfaits">Besoins non satisfaits</a>
                <a href="/besoins/critiques-materiels">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 5px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Besoins critiques (Matériel/Nature)
                </a>
                <a href="/achats/simulation">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 5px;">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Achats et simulation
                </a>
                <a href="/besoins/villes-satisfaites">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 5px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Villes satisfaites
                </a>
            </div>
        </div>

        <!-- Articles avec sous-menu -->
        <div class="nav-item has-submenu">
            <div class="nav-link-wrapper">
                <a class="nav-link" href="/articles">
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span class="link-text">Articles</span>
                </a>
                <button class="submenu-toggle" onclick="toggleSubmenu(event, 'articles-submenu')">
                    <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="submenu" id="articles-submenu">
                <a href="/articles">Liste des articles</a>
                <a href="/articles/create">Ajouter un article</a>
            </div>
        </div>

        <!-- Dons avec sous-menu -->
        <div class="nav-item has-submenu">
            <div class="nav-link-wrapper">
                <a class="nav-link" href="/dons">
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span class="link-text">Dons</span>
                </a>
                <button class="submenu-toggle" onclick="toggleSubmenu(event, 'dons-submenu')">
                    <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="submenu" id="dons-submenu">
                <a href="/dons">Liste des dons</a>
                <a href="/don/create">Ajouter un don</a>
            </div>
        </div>

        <!-- Villes avec sous-menu -->
        <div class="nav-item has-submenu">
            <div class="nav-link-wrapper">
                <a class="nav-link" href="/villes">
                    <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span class="link-text">Villes</span>
                </a>
                <button class="submenu-toggle" onclick="toggleSubmenu(event, 'villes-submenu')">
                    <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="submenu" id="villes-submenu">
                <a href="/villes">Liste des villes</a>
                <a href="/ville/create">Ajouter une ville</a>
            </div>
        </div>

        <!-- Séparateur -->
        <div style="height: 1px; background: var(--sidebar-border); margin: 1rem 1.5rem;"></div>

        <!-- Réinitialisation -->
        <div class="nav-item">
            <a class="nav-link" href="/reset" style="color: #f59e0b;">
                <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                <span class="link-text">Réinitialiser</span>
            </a>
        </div>
    </nav>
</div>

<!-- Bouton toggle pour mobile -->
<button class="sidebar-toggle" id="sidebarToggle">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<style>
:root {
    --sidebar-bg: #1e293b;
    --sidebar-header: #0f172a;
    --sidebar-hover: #334155;
    --sidebar-active: #3b82f6;
    --sidebar-text: #cbd5e1;
    --sidebar-text-active: #ffffff;
    --sidebar-border: #334155;
    --submenu-bg: #0f172a;
}

/* Sidebar professionnelle */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 280px;
    background: var(--sidebar-bg);
    box-shadow: 4px 0 12px rgba(0,0,0,.15);
    z-index: 1000;
    overflow-y: auto;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-header {
    padding: 1.75rem 1.5rem;
    background: var(--sidebar-header);
    border-bottom: 1px solid var(--sidebar-border);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    color: var(--sidebar-text-active);
    text-decoration: none;
    font-size: 1.25rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.sidebar-brand:hover {
    color: var(--sidebar-active);
    transform: translateX(2px);
}

.sidebar-brand .brand-text {
    letter-spacing: -0.02em;
}

.sidebar-nav {
    padding: 1.25rem 0;
}

.sidebar .nav-item {
    margin: 0.25rem 0.75rem;
}

/* Wrapper pour lien + bouton toggle */
.sidebar .nav-link-wrapper {
    display: flex;
    align-items: center;
    gap: 0;
    background: transparent;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.sidebar .nav-link-wrapper:hover {
    background: var(--sidebar-hover);
}

.sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 1rem;
    color: var(--sidebar-text);
    text-decoration: none;
    border-radius: 8px 0 0 8px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.95rem;
    font-weight: 500;
    position: relative;
    cursor: pointer;
    flex: 1;
}

.sidebar .has-submenu .nav-link {
    border-radius: 0;
}

.sidebar .nav-link:hover {
    color: var(--sidebar-text-active);
    transform: translateX(2px);
}

.sidebar .nav-link.active {
    background: var(--sidebar-active);
    color: var(--sidebar-text-active);
}

/* Bouton toggle pour sous-menu */
.sidebar .submenu-toggle {
    background: transparent;
    border: none;
    padding: 0.875rem 0.75rem;
    cursor: pointer;
    color: var(--sidebar-text);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 8px 8px 0;
}

.sidebar .submenu-toggle:hover {
    color: var(--sidebar-text-active);
}

.sidebar .submenu-toggle .arrow {
    transition: transform 0.3s ease;
    opacity: 0.7;
}

.sidebar .has-submenu.active .submenu-toggle .arrow {
    transform: rotate(180deg);
}

.sidebar .nav-link .icon {
    flex-shrink: 0;
    opacity: 0.9;
}

.sidebar .nav-link:hover .icon {
    opacity: 1;
}

.sidebar .nav-link .link-text {
    flex: 1;
}

/* Sous-menus */
.sidebar .submenu {
    padding: 0.5rem 0;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: var(--submenu-bg);
    border-radius: 6px;
    margin-top: 0.25rem;
}

.sidebar .has-submenu.active .submenu {
    max-height: 500px;
    margin-bottom: 0.5rem;
}

.sidebar .submenu a {
    display: block;
    padding: 0.75rem 1rem 0.75rem 3.25rem;
    color: var(--sidebar-text);
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border-radius: 4px;
    position: relative;
}

.sidebar .submenu a::before {
    content: '';
    position: absolute;
    left: 2.25rem;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--sidebar-text);
    opacity: 0.5;
    transition: all 0.2s ease;
}

.sidebar .submenu a:hover {
    color: var(--sidebar-active);
    background: rgba(59, 130, 246, 0.1);
    padding-left: 3.5rem;
}

.sidebar .submenu a:hover::before {
    background: var(--sidebar-active);
    opacity: 1;
    width: 6px;
    height: 6px;
}

/* Bouton toggle pour mobile */
.sidebar-toggle {
    position: fixed;
    top: 1.25rem;
    left: 1.25rem;
    z-index: 1001;
    background: var(--sidebar-bg);
    color: var(--sidebar-text-active);
    border: 1px solid var(--sidebar-border);
    border-radius: 8px;
    padding: 0.625rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    display: none;
    transition: all 0.3s ease;
}

.sidebar-toggle:hover {
    background: var(--sidebar-hover);
    transform: scale(1.05);
}

/* Ajuster le contenu principal */
body {
    margin-left: 280px;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #f8fafc;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .sidebar-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    body {
        margin-left: 0;
    }
}

/* Scrollbar personnalisée */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--sidebar-border);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--sidebar-hover);
}

/* Animation d'entrée */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.sidebar .nav-item {
    animation: slideIn 0.3s ease-out backwards;
}

.sidebar .nav-item:nth-child(1) { animation-delay: 0.05s; }
.sidebar .nav-item:nth-child(2) { animation-delay: 0.1s; }
.sidebar .nav-item:nth-child(3) { animation-delay: 0.15s; }
.sidebar .nav-item:nth-child(4) { animation-delay: 0.2s; }
.sidebar .nav-item:nth-child(5) { animation-delay: 0.25s; }
</style>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
// Fonction pour toggle les sous-menus (doit être définie globalement avant DOMContentLoaded)
function toggleSubmenu(event, submenuId) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.currentTarget;
    const parentItem = button.closest('.nav-item');
    
    // Fermer tous les autres sous-menus
    document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
        if (item !== parentItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle le sous-menu cliqué
    parentItem.classList.toggle('active');
}

// Toggle sidebar sur mobile et activer les liens
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar pour mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Fermer la sidebar en cliquant en dehors
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
    
    // Activer le lien actuel dans la sidebar
    const currentPath = window.location.pathname;
    
    // Vérifier les liens du menu principal
    document.querySelectorAll('.sidebar .nav-link[href]').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && href === currentPath) {
            link.classList.add('active');
            // Si c'est dans un sous-menu parent, ouvrir le sous-menu
            const parentSubmenu = link.closest('.has-submenu');
            if (parentSubmenu) {
                parentSubmenu.classList.add('active');
            }
        }
    });
    
    // Vérifier les liens des sous-menus
    document.querySelectorAll('.sidebar .submenu a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.style.color = 'var(--sidebar-active)';
            link.style.background = 'rgba(59, 130, 246, 0.1)';
            // Ouvrir le sous-menu parent
            const parentSubmenu = link.closest('.has-submenu');
            if (parentSubmenu) {
                parentSubmenu.classList.add('active');
            }
        }
    });
});
</script>
