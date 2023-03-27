<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['MODIFY_RECIPE'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['MODIFY_RECIPE'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?int $preparationTime = null;

    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?int $breakTime = null;

    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?int $cookingTime = null;

    #[ORM\Column]
    #[Groups(['MODIFY_RECIPE'])]
    private ?bool $patientOnly = false;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: IngredientRecipe::class, cascade: ['persist', 'remove'])]
    #[Groups(['MODIFY_RECIPE'])]
    private Collection $ingredientRecipes;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Notice::class, cascade: ['persist', 'remove'])]
    private Collection $notices;

    #[ORM\Column()]
    #[Groups(['MODIFY_RECIPE'])]
    private array $stages = [];

    #[ORM\ManyToMany(targetEntity: Diet::class, inversedBy: 'recipes', cascade: ['persist'])]
    #[Groups(['MODIFY_RECIPE'])]
    private Collection $diets;

    #[ORM\ManyToMany(targetEntity: Allergen::class, inversedBy: 'recipes', cascade: ['persist'])]
    #[Groups(['MODIFY_RECIPE'])]
    private Collection $allergens;


    public function __construct()
    {
        $this->ingredientRecipes = new ArrayCollection();
        $this->notices = new ArrayCollection();
        $this->diets = new ArrayCollection();
        $this->allergens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(int $preparationTime): self
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getBreakTime(): ?int
    {
        return $this->breakTime;
    }

    public function setBreakTime(int $breakTime): self
    {
        $this->breakTime = $breakTime;

        return $this;
    }

    public function getCookingTime(): ?int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(int $cookingTime): self
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    public function isPatientOnly(): ?bool
    {
        return $this->patientOnly;
    }

    public function setPatientOnly(bool $patientOnly): self
    {
        $this->patientOnly = $patientOnly;

        return $this;
    }

    /**
     * @return Collection<int, IngredientRecipe>
     */
    public function getIngredientRecipes(): Collection
    {
        return $this->ingredientRecipes;
    }

    public function addIngredientRecipe(IngredientRecipe $ingredientRecipe): self
    {
        if (!$this->ingredientRecipes->contains($ingredientRecipe)) {
            $this->ingredientRecipes->add($ingredientRecipe);
            $ingredientRecipe->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredientRecipe(IngredientRecipe $ingredientRecipe): self
    {
        if ($this->ingredientRecipes->removeElement($ingredientRecipe)) {
            // set the owning side to null (unless already changed)
            if ($ingredientRecipe->getRecipe() === $this) {
                $ingredientRecipe->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notice $notice): self
    {
        if (!$this->notices->contains($notice)) {
            $this->notices->add($notice);
            $notice->setRecipe($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): self
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getRecipe() === $this) {
                $notice->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getStages(): array
    {
        return $this->stages;
    }

    /**
     * @param array $stages
     * @return Recipe
     */
    public function setStages(array $stages): Recipe
    {
        $this->stages = $stages;
        return $this;
    }

    /**
     * @return Collection<int, Diet>
     */
    public function getDiets(): Collection
    {
        return $this->diets;
    }

    public function addDiet(Diet $diet): self
    {
        if (!$this->diets->contains($diet)) {
            $this->diets->add($diet);
        }

        return $this;
    }

    public function removeDiet(Diet $diet): self
    {
        $this->diets->removeElement($diet);

        return $this;
    }

    /**
     * @return Collection<int, Allergen>
     */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function addAllergen(Allergen $allergen): self
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens->add($allergen);
        }

        return $this;
    }

    public function removeAllergen(Allergen $allergen): self
    {
        $this->allergens->removeElement($allergen);

        return $this;
    }

    public function reIndex ($arr)
    {
        /** @var Collection<int, > $allergens */
        $allergens = array_values((array)$this->allergens);
        return array_values($arr);
    }
}
