<!-- Navbar moderne -->
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 4px rgba(0,0,0,.1);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/">
            <span style="font-size: 1.3rem;">🏠 SolidaireHub</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Tableau de bord -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="/tableau-bord">
                        📊 Tableau de bord
                    </a>
                </li>

                <!-- Besoins -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="besoinsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        📋 Besoins
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="besoinsDropdown">
                        <li><a class="dropdown-item" href="/besoins">📊 Liste des besoins</a></li>
                        <li><a class="dropdown-item" href="/besoin/create">➕ Ajouter un besoin</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/besoins/non-satisfaits">⚠️ Besoins non satisfaits</a></li>
                    </ul>
                </li>

                <!-- Dons -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="donsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        🎁 Dons
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="donsDropdown">
                        <li><a class="dropdown-item" href="/dons">📊 Liste des dons</a></li>
                        <li><a class="dropdown-item" href="/don/create">➕ Ajouter un don</a></li>
                    </ul>
                </li>

                <!-- Villes -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="villesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        🏙️ Villes
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="villesDropdown">
                        <li><a class="dropdown-item" href="/villes">📊 Liste des villes</a></li>
                        <li><a class="dropdown-item" href="/ville/create">➕ Ajouter une ville</a></li>
                    </ul>
                </li>

                <!-- Statistiques -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="/dons">
                        🎁 Dons
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar-dark .navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.95);
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
}

.navbar-dark .navbar-nav .nav-link:hover {
    color: #ffffff;
    transform: translateY(-2px);
}

.navbar-brand {
    transition: transform 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-top: 0.5rem;
}

.dropdown-item {
    padding: 0.7rem 1.5rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateX(5px);
}
</style>
