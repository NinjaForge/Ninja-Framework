<?php
/*
 * $Id: SetRevisionTask.php 521 2010-10-10 15:39:54Z stian $
 *
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
  * @version     $Id: SetRevisionTask.php 521 2010-10-10 15:39:54Z stian $
  * @package     napi.phing.tasks
  */
class SetRevisionTask extends Task
{
    /**
     * Property for File
     * @var PhingFile file
     */
    private $file;

    /**
     * Property to be set
     * @var string $property
     */
    private $property;

    /**
     * Set Property for File containing versioninformation
     * @param PhingFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
    
    /**
     * Set
     * @param $property
     * @return
     */
    public function setProperty($property)
    {
        $this->property = $property;
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
        $this->checkFile();
        $this->checkProperty();

        // load file
        $xml = simplexml_load_file($this->file);

        // set new version, overwriting the old one
        $xml = $this->setRevision($xml);

        // write the new xml to the old xml file
        $xml->asXML($this->file);
    }

    /**
     * Sets the new revision number
     *
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    private function setRevision($xml)
    {
    	$xml->revision = $this->property;

        return $xml;
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

    /**
     * checks property attribute
     * @return void
     * @throws BuildException
     */
    private function checkProperty()
    {
        if (is_null($this->property) ||
            strlen($this->property) === 0) {
            throw new BuildException('Property for revision number is not set', $this->location);
        }
    }
}