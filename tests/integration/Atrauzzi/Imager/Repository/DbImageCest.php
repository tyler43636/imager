<?php

use \IntegrationTester;
use Atrauzzi\Imager\Repository\DbImage;
use Atrauzzi\Imager\Model\Image as ImageModel;

class DbImageCest
{
    /**
     * @var \Atrauzzi\Imager\Repository\DbImage
     */
    protected $repository;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    public function _before(IntegrationTester $I)
    {
        SetupDatabase::setupTestDb();
        $this->repository = new DbImage();
        $this->imageFactory = new ImageFactory();
    }

    public function _after(IntegrationTester $I)
    {
    }

    /** @test */
    public function createAnImageRecord(IntegrationTester $I)
    {
        $imageAttributes = $this->imageFactory->getFakeImageAttributes();
        $this->repository->create($imageAttributes);

        $I->canSeeInDatabase('imager_image', $imageAttributes);
    }

    /** @test */
    public function getAnImageById(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel();
        $image->id = 1;
        $image->save();

        $retrievedImages = $this->repository->getById(1);

        $I->assertTrue($retrievedImages->id == 1);
    }

    /** @test */
    public function getAnImageBySlot(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel(['slot' => 'test-slot']);
        $image->save();

        $retrievedImage = $this->repository->getBySlot('test-slot')->first();

        $I->assertEquals('test-slot', $retrievedImage->slot);
    }

    /** @test */
    public function getAnImageWithAnImagableBySlot(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel(['slot' => 'test-slot']);
        $image->save();

        $album = new Album(['name' => 'test-album']);
        $album->save();
        $album->images()->save($image);

        $retrievedImage = $this->repository->getBySlot('test-slot', $album)->first();

        $I->assertEquals('test-slot', $retrievedImage->slot);
    }
}
