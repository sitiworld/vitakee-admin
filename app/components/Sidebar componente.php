<?php

class Sidebar
{
    private $menuItems;
    private $userPrivilege;
    private $currentPage;
    private $html; // Nueva propiedad para almacenar el HTML generado

    public function __construct(array $menuItems = [], int $userPrivilege = 99, string $currentPage = '')
    {
        $this->menuItems = $menuItems;
        $this->userPrivilege = $userPrivilege;
        $this->currentPage = $currentPage;
        $this->html = ''; // Inicializar la propiedad HTML
        $this->generateHtml(); // Generar el HTML al instanciar la clase
    }

    public function setMenuItems(array $menuItems): void
    {
        $this->menuItems = $menuItems;
        $this->generateHtml(); // Regenerar el HTML al cambiar los items
    }

    public function setUserPrivilege(int $userPrivilege): void
    {
        $this->userPrivilege = $userPrivilege;
        $this->generateHtml(); // Regenerar el HTML al cambiar el privilegio
    }

    public function setCurrentPage(string $currentPage): void
    {
        $this->currentPage = $currentPage;
        $this->generateHtml(); // Regenerar el HTML al cambiar la página actual
    }

    private function generateHtml(): void
    {
        $this->html = '<div class="left-side-menu">';
        $this->html .= '<div class="h-100 mt-1" data-simplebar>';
        $this->html .= '<div id="sidebar-menu">';
        $this->html .= '<ul id="side-menu">';

        // Verificar si el rol del usuario existe en el array de menús
        if (isset($this->menuItems[$this->userPrivilege])) {
            foreach ($this->menuItems[$this->userPrivilege] as $section) {
                // Cada $section debe ser un array con 'title' e 'items'
                if (isset($section['title']) && is_string($section['title']) && isset($section['items']) && is_array($section['items'])) {
                    // Mostrar el título del grupo
                    $this->html .= '<li class="menu-title">' . htmlspecialchars($section['title']) . '</li>';
                    // Mostrar los ítems del menú para este grupo
                    foreach ($section['items'] as $item) {
                        if (!isset($item['min_privilegio']) || $this->userPrivilege >= $item['min_privilegio']) {
                            $isActive = ($this->currentPage == $item["url"]) ? "active" : "";
                            $this->html .= '<li class="nav-item">';
                            $this->html .= '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link ' . htmlspecialchars($isActive) . '">';
                            $this->html .= '<i class="' . htmlspecialchars($item['icon']) . '"></i>';
                            $this->html .= '<span>' . htmlspecialchars($item['title']) . '</span>';
                            $this->html .= '</a>';
                            $this->html .= '</li>';
                        }
                    }
                }
            }
        } else {
            // Manejar el caso donde el rol del usuario no tiene un menú definido
            $this->html .= '<li class="nav-item"><p class="nav-link">No hay menú disponible para su rol.</p></li>';
        }

        $this->html .= '</ul>';
        $this->html .= '</div>';
        $this->html .= '<div class="clearfix"></div>';
        $this->html .= '</div>';
        $this->html .= '</div>';
    }

    public function render(): void
    {
        echo $this->html;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}