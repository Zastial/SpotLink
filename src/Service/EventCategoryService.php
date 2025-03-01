<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Enum\CategoryEnum;
use PhpParser\Node\Name;

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

    public function getIcons(array $eventCategoryMap): array
    {
        $categories = $this->categoryRepository->getCategories();
        $icons = [];

        foreach ($eventCategoryMap as $eventId => $categoryName) {
            foreach ($categories as $category) {
                if ($category->getName() === $categoryName) {
                    $icons[$eventId] = CategoryEnum::from($categoryName)->getIcon();
                    break;
                }
            }
        }

        return $icons;
    }
}
