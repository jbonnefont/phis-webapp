<?php

//**********************************************************************************************
//                                       YiiAnnotationModel.php 
//
// Author(s): Arnaud Charleroy
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: June 2018
// Contact: arnaud.charleroy@.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  June, 2018
// Subject: The Yii model for the Annotation. Used with web services
//***********************************************************************************************

namespace app\models\yiiModels;

use app\models\wsModels\WSAnnotationModel;
use app\models\wsModels\WSActiveRecord;
use Yii;

/**
 * The yii model for the annotation. 
 * Implements a customized Active Record
 *  (WSActiveRecord, for the web services access)
 * @see app\models\wsModels\WSAnnotationModel
 * @see app\models\wsModels\WSActiveRecord
 * @author Morgane Vidal <morgane.vidal@inra.fr> 
 * @author Arnaud Charleroy <arnaud.charleroy@.fr>
 */
class YiiAnnotationModel extends WSActiveRecord {

    public $label = "Annotation";

    /**
     * uri of the annotation
     *  (e.g. http://www.phenome-fppn.fr/platform/id/annotation/3ce85bf7-1d99-4831-9c13-4d7ebdafe1d6)
     * @var string
     */
    public $uri;

    const URI = "uri";
    const URI_LABEL = "URI";

    /**
     * the creation date of the annotation
     *  (e.g. 2018-06-25 15:13:59+0200)
     * @var string
     */
    public $creationDate;

    const CREATION_DATE = "creationDate";
    const CREATION_DATE_LABEL = "Date of Annotation";

    /**
     * the creator of the annotation
     *  (e.g. http://www.phenome-fppn.fr/diaphen/id/agent/acharleroy)
     * @var string
     */
    public $creator;

    const CREATOR = "creator";
    const CREATOR_LABEL = "Creator";

    /**
     * the purpose of the annotation
     *  (e.g. http://www.w3.org/ns/oa#commenting)
     * @var string
     */
    public $motivatedBy;

    const MOTIVATED_BY = "motivatedBy";
    const MOTIVATED_BY_LABEL = "Motivated by";

    /**
     * the description of the annotation
     *  (e.g. http://www.w3.org/ns/oa#commenting)
     * @var string
     */
    public $comments;

    const COMMENTS = "comments";
    const COMMENTS_LABEL = "Description";

    /**
     *  a target associate to this annotation 
     *  (e.g. http://www.phenome-fppn.fr/phenovia/2017/o1032481)
     * @var string
     */
    public $targets;

    const TARGETS = "targets";
    const TARGETS_LABEL = "Targets";

    public function __construct($pageSize = null, $page = null) {
        $date = new \DateTime();
        $this->creationDate = $date->format('Y-m-d H:i:sP');
        $this->wsModel = new WSAnnotationModel();
        $this->pageSize = ($pageSize !== null || $pageSize === "") ? $pageSize : null;
        $this->page = ($page !== null || $pageSize === "") ? $page : null;
    }

    /**
     * 
     * @inheritdoc
     */
    public function rules() {
        return [
            [[YiiAnnotationModel::URI, YiiAnnotationModel::CREATOR, YiiAnnotationModel::MOTIVATED_BY, YiiAnnotationModel::COMMENTS, YiiAnnotationModel::TARGETS], 'required'],
            [[YiiAnnotationModel::URI, YiiAnnotationModel::CREATOR, YiiAnnotationModel::MOTIVATED_BY, YiiAnnotationModel::COMMENTS, YiiAnnotationModel::TARGETS], 'safe'],
            [[YiiAnnotationModel::COMMENTS], 'string'],
            [[YiiAnnotationModel::URI, YiiAnnotationModel::CREATOR, YiiAnnotationModel::TARGETS], 'string', 'max' => 300]
        ];
    }

    /**
     * 
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            YiiAnnotationModel::URI => YiiAnnotationModel::URI_LABEL,
            YiiAnnotationModel::CREATOR => Yii::t('app', YiiAnnotationModel::CREATOR_LABEL),
            YiiAnnotationModel::MOTIVATED_BY => Yii::t('app', YiiAnnotationModel::MOTIVATED_BY_LABEL),
            YiiAnnotationModel::COMMENTS => Yii::t('app', YiiAnnotationModel::COMMENTS_LABEL),
            YiiAnnotationModel::TARGETS => Yii::t('app', YiiAnnotationModel::TARGETS_LABEL)
        ];
    }

    /**
     * Permet de remplir les attributs en fonction des informations 
     * comprises dans le tableau passé en paramètre
     * @param array $array tableau clé => valeur contenant les valeurs des attributs du projet
     */
    protected function arrayToAttributes($array) {
        $this->uri = $array[YiiAnnotationModel::URI];
        $this->creator = $array[YiiAnnotationModel::CREATOR];
        $this->comments = $array[YiiAnnotationModel::COMMENTS];
        $this->motivatedBy = $array[YiiAnnotationModel::MOTIVATED_BY];
        $this->targets = $array[YiiAnnotationModel::TARGETS];
    }

    /**
     * @return array contenant l'élément à enregistrer en base de données
     *         cette méthode est publique pour que l'utilisateur puisse choisir de l'utiliser 
     *         ou d'envoyer lui-même son propre tableau (dans le cas où il souhaite enregistrer plusieurs instances)
     */
    public function attributesToArray() {
        $elementForWebService[YiiAnnotationModel::CREATOR] = $this->creator;
        $elementForWebService[YiiAnnotationModel::MOTIVATED_BY] = $this->motivatedBy;
        $elementForWebService[YiiAnnotationModel::CREATION_DATE] = $this->creationDate;
        // For now one target can be choose
        if (isset($this->targets) && !empty($this->targets)) {
            $elementForWebService[YiiAnnotationModel::TARGETS] = $this->targets;
        }
        if (isset($this->comments) && !empty($this->comments)) {
            $elementForWebService[YiiAnnotationModel::COMMENTS] = $this->comments;
        }
        return $elementForWebService;
    }

    /**
     * Find an annotation by this uri
     * @param string $sessionToken
     * @param string $uri
     * @return mixed l'objet s'il existe, un message sinon
     */
    public function findByURI($sessionToken, $uri) {
        $params = [];
        if ($this->pageSize !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE_SIZE] = $this->pageSize;
        }
        if ($this->page !== null) {
            $params[\app\models\wsModels\WSConstants::PAGE] = $this->page;
        }

        $requestRes = $this->wsModel->getAnnotationByURI($sessionToken, $uri, $params);
        if (!is_string($requestRes)) {
            if (isset($requestRes[\app\models\wsModels\WSConstants::TOKEN])) {
                return $requestRes;
            } else {
                $this->arrayToAttributes($requestRes);
                return true;
            }
        } else {
            return $requestRes;
        }
    }

}
