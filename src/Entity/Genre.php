<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Genrename = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenrename(): ?string
    {
        return $this->Genrename;
    }

    public function setGenrename(string $Genrename): self
    {
        $this->Genrename = $Genrename;

        return $this;
    }
    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setGenre($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getGenre() === $this) {
                $product->setGenre(null);
            }
        }

        return $this;
    }
}
