<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Entity\Category;

/**
 * Service de gestion des catégories des événements.
 */
class EventCategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Récupérer les couleurs des marqueurs des événements.
     * @param array $eventCategoryMap Un tableau des catégories des événements.
     * @return array Un tableau des couleurs des marqueurs des événements.
     */
    public function getMarkerColors(array $eventCategoryMap): array
    {
        $categories = $this->categoryRepository->getCategories();
        $markerColors = [];

        foreach ($eventCategoryMap as $eventId => $categoryName) {
            foreach ($categories as $category) {
                if ($category->getName() === $categoryName) {
                    $markerColors[$eventId] = $category->getMarkerColor();
                    break;
                }
            }
        }

        return $markerColors;
    }
}
