<?php

use Atrauzzi\Imager\Model\Image as ImageModel;

class ImageModelCest {

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    public function _before(IntegrationTester $I)
    {
        SetupDatabase::setupTestDb();
        $this->imageFactory = new ImageFactory();
    }

    public function _after(IntegrationTester $I)
    {

    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function createARelationshipWithImagableModels(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel();
        $image->save();
        $album = Album::create(['name' => 'test album']);
        $album->images()->save($image);

        // grab the image through the album model
        $relatedImage = $album->images()->first();

        $I->assertTrue($relatedImage->imageable_type == 'Album');
    }

    /**
     * Test 'scopeForImageable'
     * @test
     * @param IntegrationTester $I
     */
    public function scopeImagesByImagable(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel();
        $image->save();
        $album = Album::create(['name' => 'test album']);
        $album->images()->save($image);

        $releatedImage = ImageModel::forImageable(
            get_class($album),
            $album->getKey()
        )->first();

        $I->assertEquals('Album', get_class($releatedImage->imageable()->first()));
    }

    /**
     * Test 'scopeInSlot'
     * @test
     */
    public function scopeImagesBySlot(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel(['slot' => 'test-slot']);
        $image->save();

        $slottedImages = ImageModel::inSlot('test-slot')->first();

        $I->assertEquals('test-slot', $slottedImages->slot);
    }

    /**
     * Test 'scopeNotInSlot'
     * @test
     */
    public function scopeImagesByNotInSlot(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel(['slot' => 'test-slot']);
        $image->save();

        $slottedImages = ImageModel::notInSlot('test-slot')->first();

        $I->assertEquals(null, $slottedImages);
    }

    /**
     * Test 'scopeWithoutSlot'
     * @test
     */
    public function scopeImagesWithoutSlot(IntegrationTester $I)
    {
        $image = $this->imageFactory->getFakeImageModel(['slot' => null]);
        $image->save();

        $unSlottedImages = ImageModel::withoutSlot()->first();

        $I->assertEquals(null, $unSlottedImages->slot);
    }

    /**
     * Test 'scopeUnattached'
     * @test
     */
    public function scopeUnattachedImages(IntegrationTester $I)
    {
        $attachedImage = $this->imageFactory->getFakeImageModel();
        $attachedImage->save();
        $unattachedImage = $this->imageFactory->getFakeImageModel();
        $unattachedImage->save();

        $album = Album::create(['name' => 'test album']);
        $album->images()->save($attachedImage);

        $image = ImageModel::unattached()->first();

        $I->assertEquals(null, $image->imageable_id);
    }

    /**
     * Test 'scopeAttached'
     * @test
     */
    public function scopeAttachedImages(IntegrationTester $I)
    {
        $attachedImage = $this->imageFactory->getFakeImageModel();
        $attachedImage->save();
        $unattachedImage = $this->imageFactory->getFakeImageModel();
        $unattachedImage->save();

        $album = Album::create(['name' => 'test album']);
        $album->images()->save($attachedImage);

        $image = ImageModel::attached()->first();

        $I->assertEquals($album->id, $image->imageable_id);
    }

    /**
     * Test 'scopeHighestRes'
     * @test
     */
    public function scopeHighestResImages(IntegrationTester $I)
    {
        $smallerImage = $this->imageFactory->getFakeImageModel(['height' => 200, 'width' => 200]);
        $smallerImage->save();
        $largerImage = $this->imageFactory->getFakeImageModel(['height' => 300, 'width' => 300]);
        $largerImage->save();

        $images = ImageModel::highestRes()->get();

        $I->assertLessThan(
            $images[0]->height + $images[0]->width,
            $images[1]->height + $images[1]->width
        );
    }

    /**
     * Test 'scopeRandom'
     * @test
     */
    public function scopeRandomImage(IntegrationTester $I)
    {
        // @todo
    }
}
