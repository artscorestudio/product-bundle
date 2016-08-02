<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Model\Brand;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use ASF\ProductBundle\Validator\Constraints as BrandAssert;
use ASF\ProductBundle\Model\Product\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Brand Model.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @ORM\Entity(repositoryClass="ASF\ProductBundle\Repository\BrandRepository")
 * @ORM\Table(name="asf_product_brand")
 * @ORM\HasLifecycleCallbacks
 * 
 * @BrandAssert\BrandClass
 */
abstract class BrandModel implements BrandInterface
{
    /**
     * All brand's states are hardcoded in constantes.
     * For historical features reasons, products are not completelly removed form the DB.
     */
    const STATE_DRAFT = 'draf';
    const STATE_WAITING = 'waiting';
    const STATE_PUBLISHED = 'published';
    const STATE_DELETED = 'deleted';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false)
     * 
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @GRID\Column(title="asf.product.brand_name", defaultOperator="like", operatorsVisible=false)
     * 
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @GRID\Column(visible=false)
     * 
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Choice(callback = "getStates")
     * @GRID\Column(title="asf.product.state", filter="select",  selectFrom="values", values={
     *     BrandModel::STATE_DRAFT = "draft",
     *     BrandModel::STATE_WAITING = "waiting",
     *     BrandModel::STATE_PUBLISHED = "published",
     *     BrandModel::STATE_DELETED = "deleted"
     * }, defaultOperator="eq", operatorsVisible=false)
     * 
     * @var string
     */
    protected $state;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="brand", cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     * @GRID\Column(visible=false)
     * 
     * @var ArrayCollection
     */
    protected $products;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @GRID\Column(visible=false)
     * 
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @GRID\Column(visible=false)
     * 
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @GRID\Column(visible=false)
     * 
     * @var \DateTime
     */
    protected $deletedAt;

    public function __construct()
    {
        $this->state = self::STATE_DRAFT;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->products = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setName()
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getState()
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setState()
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getProducts()
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::addProduct()
     */
    public function addProduct(ProductInterface $product)
    {
        $this->products->add($product);

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::removeProduct()
     */
    public function removeProduct(ProductInterface $product)
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getCreatedAt()
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setCreatedAt()
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->createdAt = $created_at;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getUpdatedAt()
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setUpdatedAt()
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getDeletedAt()
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setDeletedAt()
     */
    public function setDeletedAt(\DateTime $deleted_at)
    {
        $this->deletedAt = $deleted_at;

        return $this;
    }

    /**
     * Returns states for validators.
     *
     * @return array
     */
    public static function getStates()
    {
        return array(
            self::STATE_DRAFT,
            self::STATE_WAITING,
            self::STATE_PUBLISHED,
            self::STATE_DELETED,
        );
    }

    /**
     * @ORM\PrePersist
     * @return void
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     * @return void
     */
    public function onPreUpdate()
    {
        if ( self::STATE_DELETED === $this->state ) {
            $this->deletedAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }
}
