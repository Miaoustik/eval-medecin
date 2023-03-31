<?php

namespace App\Entity;

use App\Repository\IngredientRecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRecipeRepository::class)]
class IngredientRecipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'ingredientRecipes')]
    #[ORM\JoinColumn(nullable: false, )]
    #[Groups(['MODIFY_RECIPE'])]
    private ?Ingredient $ingredient = null;

    #[ORM\ManyToOne(inversedBy: 'ingredientRecipes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['POST_admin_createRecipe'])]
    private ?Recipe $recipe = null;

    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?string $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
