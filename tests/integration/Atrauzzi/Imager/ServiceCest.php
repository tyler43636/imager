<?php

use \IntegrationTester;

use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Foundation\Application;
use Atrauzzi\Imager\Service;
use Atrauzzi\Imager\Storage\FileNameGenerator;

class ServiceCest
{
    /**
     * The configuration used for the filesystem driver
     * @var array
     */
    protected $driverConfig = [
        'public_path' => 'http://fakedomain.example.com',
        'filesystem_root' => 'tests/_output/images/'
    ];

    /**
     * @var \Atrauzzi\Imager\Service
     */
    protected $service;

    /**
     * @var \Atrauzzi\Imager\Storage\FileNameGenerator
     */
    protected $fileNameGenerator;

    /**
     * @var \ImageFactory
     */
    protected $imageFactory;

    public function _before(IntegrationTester $I)
    {
        SetupDatabase::setupTestDb();
        $this->service = $this->bootstrapImagerService();
        $this->fileNameGenerator = new FileNameGenerator();
        $this->imageFactory = new ImageFactory();
    }

    public function _after(IntegrationTester $I)
    {
        $I->cleanDir('tests/_output/images/');
    }

    /** @test */
    public function getAnImageById(IntegrationTester $I)
    {
        $testImage = $this->imageFactory->getFakeImageModel(['width' => 500]);
        $testImage->save();

        $image = $this->service->getById($testImage->id);

        $I->assertEquals(500, $image->width);
    }

    /** @test */
    public function getAnImageBySlot(IntegrationTester $I)
    {
        $testImage = $this->imageFactory->getFakeImageModel(['slot' => 'test-slot']);
        $testImage->save();

        $image = $this->service->getBySlot('test-slot');

        $I->assertEquals('test-slot', $image->slot);
    }

    /** @test */
    public function getAnImagesPublicUri(IntegrationTester $I)
    {
        $this->saveAnImageFromFile($I);

        $imageRepo = new \Atrauzzi\Imager\Repository\DbImage();
        $image = $imageRepo->getById(1);

        $url = $this->service->getPublicUri($image);
        $fileName = $this->fileNameGenerator->generateFileName($image);

        $I->assertEquals($this->driverConfig['public_path'] . '/' . $fileName, $url);
    }

    /** @test */
    public function getAnImagesPublicUriById(IntegrationTester $I)
    {
        $this->saveAnImageFromFile($I);

        $imageRepo = new \Atrauzzi\Imager\Repository\DbImage();
        $image = $imageRepo->getById(1);

        $url = $this->service->getPublicUriById($image->id);
        $fileName = $this->fileNameGenerator->generateFileName($image);

        $I->assertEquals($this->driverConfig['public_path'] . '/' . $fileName, $url);
    }

    /** @test */
    public function getAnImagesPublicUriBySlot(IntegrationTester $I)
    {
        $this->saveAnImageFromFile($I);

        $imageRepo = new \Atrauzzi\Imager\Repository\DbImage();
        $image = $imageRepo->getById(1);

        $url = $this->service->getPublicUriBySlot('test-slot');
        $fileName = $this->fileNameGenerator->generateFileName($image);

        $I->assertEquals($this->driverConfig['public_path'] . '/' . $fileName, $url);
    }

    /** @test */
    public function saveAnImageFromFile(IntegrationTester $I)
    {
        $file = $this->getImageFile();
        $image = $this->service->saveFromFile($file, null, ['slot' => 'test-slot']);

        $fileName = $this->fileNameGenerator->generateFileName($image);
        $I->seeFileFound('tests/_output/images/' . $fileName);
    }

    /** @test */
    public function saveAnImageFromUri(IntegrationTester $I)
    {
        $image = $this->service->saveFromUri('http://placehold.it/400x300&text=imager');

        $fileName = $this->fileNameGenerator->generateFileName($image);
        $I->seeFileFound('tests/_output/images/' . $fileName);
    }

    /** @test */
    public function deleteAnImage(IntegrationTester $I)
    {
        $file = $this->getImageFile();
        $image = $this->service->saveFromFile($file);

        $fileName = $this->fileNameGenerator->generateFileName($image);
        $I->seeFileFound('tests/_output/images/' . $fileName);

        $this->service->delete($image);
        $I->dontSeeFileFound('tests/_output/images/' . $fileName);
    }

    /** @test */
    public function deleteAnImageById(IntegrationTester $I)
    {
        $file = $this->getImageFile();
        $image = $this->service->saveFromFile($file);

        $fileName = $this->fileNameGenerator->generateFileName($image);
        $I->seeFileFound('tests/_output/images/' . $fileName);

        $this->service->deleteById($image->id);
        $I->dontSeeFileFound('tests/_output/images/' . $fileName);
    }

    /**
     * @return Atrauzzi\Imager\Service
     */
    protected function bootstrapImagerService()
    {
        $app = new Application();

        $fileNameGenerator = new Atrauzzi\Imager\Storage\FileNameGenerator();

        $storageDrivers['Atrauzzi\Imager\Storage\Filesystem'] = new \Atrauzzi\Imager\Storage\Filesystem($fileNameGenerator);
        $storageDrivers['Atrauzzi\Imager\Storage\Filesystem']->setPublicPrefix($this->driverConfig['public_path']);
        $storageDrivers['Atrauzzi\Imager\Storage\Filesystem']->setRoot($this->driverConfig['filesystem_root']);

        $service = new Service(
            new Atrauzzi\Imager\Repository\DbImage(),
            new Intervention\Image\ImageManager(),
            $app,
            $storageDrivers
        );

        return $service;
    }

    /**
     * @return File
     */
    protected function getImageFile()
    {
        copy('tests/_data/images/imager.gif', 'tests/_data/imager.gif');
        $file = new File('tests/_data/imager.gif');
        return $file;
    }
}
