<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Entity\Category;

class EventCategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

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
