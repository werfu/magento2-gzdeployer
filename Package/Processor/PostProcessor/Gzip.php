<?php

namespace Werfu\GZDeployer\Package\Processor\PostProcessor;

use Magento\Deploy\Package\Package;
use Magento\Deploy\Package\PackageFile;
use Magento\Deploy\Package\Processor\ProcessorInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;


class GZip implements ProcessorInterface
{
    const EXTENSIONS_TO_PROCESS = ['css', 'js', 'csv', 'txt', 'tsv', 'html'];
    const COMPRESSION_LEVEL = 9;
    const READ_CHUNK_SIZE = 512 * 1024;

    /**
     * Static content directory writable interface
     *
     * @var Filesystem\Directory\WriteInterface
     */
    private $staticDir;

    /**
     * Deployment procedure options
     *
     * @var array
     */
    private $options = [];


    /**
     * Logger interface
     * 
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * GZip constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, \Psr\Log\LoggerInterface $logger)
    {
        $this->staticDir = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $this->logger = $logger;
    }

       /**
     * @inheritdoc
     */
    public function process(Package $package, array $options)
    {
        $this->options = $options;

        /// TODO Add option to disable GZip post-processing
        // if ($this->options[DeployStaticOptions::NO_CSS] === true) {
        //     return false;
        // }

        $urlMap = [];
        /** @var PackageFile $file */
        foreach (array_keys($package->getMap()) as $fileId) {
            $filePath = str_replace(\Magento\Framework\View\Asset\Repository::FILE_ID_SEPARATOR, '/', $fileId);
            if (in_array(strtolower(pathinfo($fileId, PATHINFO_EXTENSION)), self::EXTENSIONS_TO_PROCESS)) {
                
                // $urlMap = $this->parseCss(
                //     $urlMap,
                //     $filePath,
                //     $package->getPath(),
                //     $this->staticDir->readFile(
                //         $this->minification->addMinifiedSign($package->getPath() . '/' . $filePath)
                //     ),
                //     $package
                // );

                if(!$this->staticDir->isExist($package->getPath() . '/' . $filePath))
                    continue;

                $srcPath = $this->staticDir->getAbsolutePath($package->getPath() . '/' . $filePath);
                $destPath =  $this->staticDir->getAbsolutePath($package->getPath() . '/' . $filePath . '.gz');

                // Delete the existing compressed file
                if(\file_exists($destPath)) unlink($destPath);

                if($output = gzopen($destPath, 'wb' . self::COMPRESSION_LEVEL))
                {
                    if($input = fopen($srcPath, 'rb'))
                    {
                        while(!feof($input))
                        {
                            gzwrite($output, fread($input, self::READ_CHUNK_SIZE));
                        }
                        fclose($input);
                    }
                    gzclose($output);
                }
            }
        }

        return true;        
    }

}