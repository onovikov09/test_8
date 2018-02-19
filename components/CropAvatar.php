<?php
class CropAvatar {
    private $src;
    private $data;
    private $dst;
    private $type;
    private $extension;
    private $msg;

    function __construct($src, $data, $file, $src, $dst) {
        $this->src = $src;
        $this->dst = $dst;
        $this->setData($data);
        $this->setFile($file);
        $this->crop($this->src, $this->dst, $this->data);
    }

    private function setData($data) {
        if (!empty($data)) {
            $this->data = json_decode(stripslashes($data));
        }
    }

    private function setFile($file) {
        /**
         * @var CUploadedFile $file
         */
        $errorCode = $file->getError();
        if ($errorCode === UPLOAD_ERR_OK) {
            $type = exif_imagetype($file->getTempName());

            if ($type) {
                $extension = image_type_to_extension($type);

                if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                    $result = $file->saveAs($this->src);
                    if ($result) {
                        $this->type = $type;
                        $this->extension = $extension;
                    } else {
                        $this->msg = 'Failed to save file';
                    }
                } else {
                    $this->msg = 'Please upload image with the following types: JPG, PNG, GIF';
                }
            } else {
                $this->msg = 'Please upload image file';
            }
        } else {
            $this->msg = $this->codeToMessage($errorCode);
        }
    }

    private function crop($src, $dst, $data) {
        if (!empty($src) && !empty($dst) && !empty($data)) {
            switch ($this->type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }

            if (empty($src_img)) {
                $this->msg = "Failed to read the image file";
                return;
            }

            $dst_img = imagecreatetruecolor(110, 155);
            $result = imagecopyresampled($dst_img, $src_img, 0, 0, $data->x, $data->y, 110, 155, $data->width, $data->height);

            if ($result) {
                switch ($this->type) {
                    case IMAGETYPE_GIF:
                        $result = imagegif($dst_img, $dst);
                        break;

                    case IMAGETYPE_JPEG:
                        $result = imagejpeg($dst_img, $dst);
                        break;

                    case IMAGETYPE_PNG:
                        $result = imagepng($dst_img, $dst);
                        break;
                }

                if (!$result) {
                    $this->msg = "Failed to save the cropped image file";
                }
            } else {
                $this->msg = "Failed to crop the image file";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    private function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                break;

            case UPLOAD_ERR_PARTIAL:
                $message = 'The uploaded file was only partially uploaded';
                break;

            case UPLOAD_ERR_NO_FILE:
                $message = 'No file was uploaded';
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing a temporary folder';
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk';
                break;

            case UPLOAD_ERR_EXTENSION:
                $message = 'File upload stopped by extension';
                break;

            default:
                $message = 'Unknown upload error';
        }

        return $message;
    }

    public function getResult() {
        return F::getWebPath(basename(empty($this->data) ? $this->src : $this->dst));
    }

    public function getMsg() {
        return $this->msg;
    }
}
