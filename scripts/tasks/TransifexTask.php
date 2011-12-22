<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */
require_once 'phing/Task.php';

 /**
  * SetRevisionTask
  *
  * Updates an xml manifest with the revision number from the supplied property
  *
  * @author      Stian Didriksen <stian@ninjaforge.com>
  * @package     napi.phing.tasks
  */
class TransifexTask extends Task
{
    /**
     * Root folder for the code, usually svn/trunk/code
     * @var PhingDir dir
     */
    private $dir;

    /**
     * Property to be set
     * @var PhingFile file
     */
    private $file;

    /**
     * Set the dir
     * @param $dir
     * @return
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * Set Property for the manifest file
     * @param PhingFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Main-Method for the Task
     *
     * @return  void
     * @throws  BuildException
     */
    public function main()
    {
        // check supplied attributes
        $this->checkDir();
        $this->checkFile();

        // load file
        $xml = simplexml_load_file($this->file);

        // Just set them
        $xml = $this->setLanguages($xml);

        // write the new xml to the old xml file, using tidy if installed
        if(function_exists('tidy_repair_string')) {
            file_put_contents($this->file, tidy_repair_string($xml->asXML(), array( 
                'output-xml' => true, 
                'input-xml' => true,
                'indent' => true,
                'indent-spaces' => 4,
                'wrap' => 0,
                'vertical-space' => true,
                'output-bom' => false,
                'newline' => 'LF',
                'char-encoding' => 'utf8'
            )));
        } else {
            $xml->asXML($this->file);
        }
        
    }

    /**
     * Sets the language tags
     *
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    private function setLanguages($xml)
    {
        unset($xml->administration->languages);
        $languages           = $xml->administration->addChild('languages');
        $languages['folder'] = 'administrator/language';

        //First grab the admin files
        $dir = new DirectoryIterator($this->dir.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'language');
        $this->iterateOverLanguages($languages, $dir);
        
        
        unset($xml->languages);
        $languages           = $xml->addChild('languages');
        $languages['folder'] = 'language';
        
        //Next grab the site files
        $dir = new DirectoryIterator($this->dir.DIRECTORY_SEPARATOR.'language');
        $this->iterateOverLanguages($languages, $dir);


        return $xml;
    }
    
    /**
     * Iterate them
     *
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    private function iterateOverLanguages($languages, $dir)
    {
        foreach($dir as $folder)
        {
            $code = $folder->getFilename();
            if($folder->isDot() || !$folder->isDir() || !preg_match('#^[a-z]{2,3}\-[a-z]{2,3}$#i', $code)) continue;
            
            foreach(new DirectoryIterator($folder->getPathname()) as $file)
            {
                $filename        = $file->getFilename();
                if(!$file->isFile() || !preg_match('#\.ini$#', $filename)) continue;
                $language        = $languages->addChild('language', $code.'/'.$filename);
                $language['tag'] = $code;
            }
        }
    }
    
    /**
     * checks dir attribute
     * @return void
     * @throws BuildException
     */
    private function checkDir()
    {
        // check Dir
        if ($this->dir === null ||
        strlen($this->dir) == 0) {
            throw new BuildException('You must specify a valid directory.', $this->location);
        }

        if (!is_dir($this->dir)) {
            throw new BuildException('Directory do not exist.', $this->location);
        }
        
        if (!is_dir($this->dir.DIRECTORY_SEPARATOR.'language')) {
            throw new BuildException('Supplied directory does not have a language folder.', $this->location);
        }
        
        if (!is_dir($this->dir.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'language')) {
            throw new BuildException('Supplied directory does not have a administrator/language folder.', $this->location);
        }
    }

    /**
     * checks file attribute
     * @return void
     * @throws BuildException
     */
    private function checkFile()
    {
        // check File
        if ($this->file === null ||
        strlen($this->file) == 0) {
            throw new BuildException('You must specify an xml file containing the <revision> tag.', $this->location);
        }

        $content = file_get_contents($this->file);
        if (strlen($content) == 0) {
            throw new BuildException(sprintf('Supplied file %s is empty', $this->file), $this->location);
        }
    }
}