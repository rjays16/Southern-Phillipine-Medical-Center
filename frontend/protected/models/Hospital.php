<?php
namespace SegHis\models;
/**
 * This is the model class for table "seg_hospital_info".
 *
 * The followings are the available columns in table 'seg_hospital_info':
 * @property string $hosp_type
 * @property string $hosp_id
 * @property string $hosp_name
 * @property double $house_case_dailyrate
 * @property string $addr_no_street
 * @property integer $brgy_nr
 * @property string $zip_code
 * @property string $hosp_addr1
 * @property string $hosp_addr2
 * @property string $hosp_agency
 * @property string $hosp_country
 * @property integer $accom_hrs_cutoff
 * @property double $pcf
 * @property double $housecase_pcf
 * @property double $hc_rvuadjustment
 * @property integer $default_city
 * @property string $authrep
 * @property string $designation
 * @property double $pf_defaultrate
 * @property string $bed_capacity
 * @property string $tax_acctno
 * @property string $LIS_protocol_type
 * @property string $LIS_protocol_get_type
 * @property string $LIS_protocol
 * @property string $LIS_address
 * @property string $LIS_address_local
 * @property integer $LIS_port
 * @property integer $LIS_port_get
 * @property string $LIS_folder_path
 * @property string $LIS_folder_path_local
 * @property string $LIS_folder_path_inbox
 * @property string $LIS_folder_path_pdf
 * @property integer $LIS_service_timeout
 * @property string $LIS_HL7_extension
 * @property string $LIS_username
 * @property string $LIS_password
 * @property string $LIS_address_dms
 * @property string $LIS_folder_path_pdf_dms
 * @property string $LIS_username_dms
 * @property string $LIS_password_dms
 * @property string $connection_type
 * @property string $EHR_address
 * @property string $EHR_directory
 * @property string $EHR_username
 * @property string $EHR_password
 * @property string $EMR_address
 * @property string $EMR_directory
 * @property string $EMR_username
 * @property string $EMR_password
 * @property string $PACS_protocol_type
 * @property string $PACS_protocol_get_type
 * @property string $PACS_protocol
 * @property string $PACS_address
 * @property string $PACS_address_local
 * @property integer $PACS_port
 * @property integer $PACS_port_get
 * @property string $PACS_folder_path
 * @property string $PACS_folder_path_local
 * @property string $PACS_folder_path_inbox
 * @property integer $PACS_service_timeout
 * @property string $PACS_HL7_extension
 * @property string $PACS_username
 * @property string $PACS_password
 * @property string $PACS_connection_type
 * @property string $INV_address
 * @property string $INV_directory
 * @property string $INV_api_key
 */
class Hospital extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_hospital_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hosp_type, hosp_id, hosp_name, addr_no_street, brgy_nr, zip_code, hosp_addr1, hosp_addr2, hosp_agency, hosp_country, authrep, designation, bed_capacity, tax_acctno', 'required'),
			array('brgy_nr, accom_hrs_cutoff, default_city, LIS_port, LIS_port_get, LIS_service_timeout, PACS_port, PACS_port_get, PACS_service_timeout', 'numerical', 'integerOnly'=>true),
			array('house_case_dailyrate, pcf, housecase_pcf, hc_rvuadjustment, pf_defaultrate', 'numerical'),
			array('hosp_type, bed_capacity, LIS_protocol, LIS_HL7_extension, PACS_protocol, PACS_HL7_extension', 'length', 'max'=>10),
			array('hosp_id', 'length', 'max'=>5),
			array('hosp_name, addr_no_street, hosp_addr1, hosp_addr2, designation', 'length', 'max'=>80),
			array('zip_code, tax_acctno', 'length', 'max'=>15),
			array('hosp_agency, hosp_country', 'length', 'max'=>100),
			array('authrep', 'length', 'max'=>120),
			array('LIS_protocol_type, LIS_protocol_get_type, PACS_protocol_type, PACS_protocol_get_type', 'length', 'max'=>3),
			array('LIS_address, LIS_address_local, PACS_address, PACS_address_local', 'length', 'max'=>20),
			array('LIS_folder_path, LIS_folder_path_local, LIS_folder_path_inbox, LIS_folder_path_pdf, LIS_folder_path_pdf_dms, EHR_directory, EMR_directory, PACS_folder_path, PACS_folder_path_local, PACS_folder_path_inbox, INV_directory', 'length', 'max'=>200),
			array('LIS_username, LIS_password, LIS_address_dms, LIS_username_dms, LIS_password_dms, EHR_address, EHR_username, EHR_password, EMR_address, EMR_username, EMR_password, PACS_username, PACS_password, INV_address', 'length', 'max'=>50),
			array('connection_type, PACS_connection_type', 'length', 'max'=>4),
			array('INV_api_key', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('hosp_type, hosp_id, hosp_name, house_case_dailyrate, addr_no_street, brgy_nr, zip_code, hosp_addr1, hosp_addr2, hosp_agency, hosp_country, accom_hrs_cutoff, pcf, housecase_pcf, hc_rvuadjustment, default_city, authrep, designation, pf_defaultrate, bed_capacity, tax_acctno, LIS_protocol_type, LIS_protocol_get_type, LIS_protocol, LIS_address, LIS_address_local, LIS_port, LIS_port_get, LIS_folder_path, LIS_folder_path_local, LIS_folder_path_inbox, LIS_folder_path_pdf, LIS_service_timeout, LIS_HL7_extension, LIS_username, LIS_password, LIS_address_dms, LIS_folder_path_pdf_dms, LIS_username_dms, LIS_password_dms, connection_type, EHR_address, EHR_directory, EHR_username, EHR_password, EMR_address, EMR_directory, EMR_username, EMR_password, PACS_protocol_type, PACS_protocol_get_type, PACS_protocol, PACS_address, PACS_address_local, PACS_port, PACS_port_get, PACS_folder_path, PACS_folder_path_local, PACS_folder_path_inbox, PACS_service_timeout, PACS_HL7_extension, PACS_username, PACS_password, PACS_connection_type, INV_address, INV_directory, INV_api_key', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'hosp_type' => 'Hosp Type',
			'hosp_id' => 'Hosp',
			'hosp_name' => 'Hosp Name',
			'house_case_dailyrate' => 'House Case Dailyrate',
			'addr_no_street' => 'Addr No Street',
			'brgy_nr' => 'Brgy Nr',
			'zip_code' => 'Zip Code',
			'hosp_addr1' => 'Hosp Addr1',
			'hosp_addr2' => 'Hosp Addr2',
			'hosp_agency' => 'Hosp Agency',
			'hosp_country' => 'Hosp Country',
			'accom_hrs_cutoff' => 'Accom Hrs Cutoff',
			'pcf' => 'Pcf',
			'housecase_pcf' => 'Housecase Pcf',
			'hc_rvuadjustment' => 'Hc Rvuadjustment',
			'default_city' => 'Default City',
			'authrep' => 'Authrep',
			'designation' => 'Designation',
			'pf_defaultrate' => 'Pf Defaultrate',
			'bed_capacity' => 'Bed Capacity',
			'tax_acctno' => 'Tax Acctno',
			'LIS_protocol_type' => 'Lis Protocol Type',
			'LIS_protocol_get_type' => 'Lis Protocol Get Type',
			'LIS_protocol' => 'Lis Protocol',
			'LIS_address' => 'Lis Address',
			'LIS_address_local' => 'Lis Address Local',
			'LIS_port' => 'Lis Port',
			'LIS_port_get' => 'Lis Port Get',
			'LIS_folder_path' => 'Lis Folder Path',
			'LIS_folder_path_local' => 'Lis Folder Path Local',
			'LIS_folder_path_inbox' => 'Lis Folder Path Inbox',
			'LIS_folder_path_pdf' => 'Lis Folder Path Pdf',
			'LIS_service_timeout' => 'Lis Service Timeout',
			'LIS_HL7_extension' => 'Lis Hl7 Extension',
			'LIS_username' => 'Lis Username',
			'LIS_password' => 'Lis Password',
			'LIS_address_dms' => 'Lis Address Dms',
			'LIS_folder_path_pdf_dms' => 'Lis Folder Path Pdf Dms',
			'LIS_username_dms' => 'Lis Username Dms',
			'LIS_password_dms' => 'Lis Password Dms',
			'connection_type' => 'Connection Type',
			'EHR_address' => 'Ehr Address',
			'EHR_directory' => 'Ehr Directory',
			'EHR_username' => 'Ehr Username',
			'EHR_password' => 'Ehr Password',
			'EMR_address' => 'Emr Address',
			'EMR_directory' => 'Emr Directory',
			'EMR_username' => 'Emr Username',
			'EMR_password' => 'Emr Password',
			'PACS_protocol_type' => 'Pacs Protocol Type',
			'PACS_protocol_get_type' => 'Pacs Protocol Get Type',
			'PACS_protocol' => 'Pacs Protocol',
			'PACS_address' => 'Pacs Address',
			'PACS_address_local' => 'Pacs Address Local',
			'PACS_port' => 'Pacs Port',
			'PACS_port_get' => 'Pacs Port Get',
			'PACS_folder_path' => 'Pacs Folder Path',
			'PACS_folder_path_local' => 'Pacs Folder Path Local',
			'PACS_folder_path_inbox' => 'Pacs Folder Path Inbox',
			'PACS_service_timeout' => 'Pacs Service Timeout',
			'PACS_HL7_extension' => 'Pacs Hl7 Extension',
			'PACS_username' => 'Pacs Username',
			'PACS_password' => 'Pacs Password',
			'PACS_connection_type' => 'Pacs Connection Type',
			'INV_address' => 'Inv Address',
			'INV_directory' => 'Inv Directory',
			'INV_api_key' => 'Inv Api Key',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return \CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new \CDbCriteria;

		$criteria->compare('hosp_type',$this->hosp_type,true);
		$criteria->compare('hosp_id',$this->hosp_id,true);
		$criteria->compare('hosp_name',$this->hosp_name,true);
		$criteria->compare('house_case_dailyrate',$this->house_case_dailyrate);
		$criteria->compare('addr_no_street',$this->addr_no_street,true);
		$criteria->compare('brgy_nr',$this->brgy_nr);
		$criteria->compare('zip_code',$this->zip_code,true);
		$criteria->compare('hosp_addr1',$this->hosp_addr1,true);
		$criteria->compare('hosp_addr2',$this->hosp_addr2,true);
		$criteria->compare('hosp_agency',$this->hosp_agency,true);
		$criteria->compare('hosp_country',$this->hosp_country,true);
		$criteria->compare('accom_hrs_cutoff',$this->accom_hrs_cutoff);
		$criteria->compare('pcf',$this->pcf);
		$criteria->compare('housecase_pcf',$this->housecase_pcf);
		$criteria->compare('hc_rvuadjustment',$this->hc_rvuadjustment);
		$criteria->compare('default_city',$this->default_city);
		$criteria->compare('authrep',$this->authrep,true);
		$criteria->compare('designation',$this->designation,true);
		$criteria->compare('pf_defaultrate',$this->pf_defaultrate);
		$criteria->compare('bed_capacity',$this->bed_capacity,true);
		$criteria->compare('tax_acctno',$this->tax_acctno,true);
		$criteria->compare('LIS_protocol_type',$this->LIS_protocol_type,true);
		$criteria->compare('LIS_protocol_get_type',$this->LIS_protocol_get_type,true);
		$criteria->compare('LIS_protocol',$this->LIS_protocol,true);
		$criteria->compare('LIS_address',$this->LIS_address,true);
		$criteria->compare('LIS_address_local',$this->LIS_address_local,true);
		$criteria->compare('LIS_port',$this->LIS_port);
		$criteria->compare('LIS_port_get',$this->LIS_port_get);
		$criteria->compare('LIS_folder_path',$this->LIS_folder_path,true);
		$criteria->compare('LIS_folder_path_local',$this->LIS_folder_path_local,true);
		$criteria->compare('LIS_folder_path_inbox',$this->LIS_folder_path_inbox,true);
		$criteria->compare('LIS_folder_path_pdf',$this->LIS_folder_path_pdf,true);
		$criteria->compare('LIS_service_timeout',$this->LIS_service_timeout);
		$criteria->compare('LIS_HL7_extension',$this->LIS_HL7_extension,true);
		$criteria->compare('LIS_username',$this->LIS_username,true);
		$criteria->compare('LIS_password',$this->LIS_password,true);
		$criteria->compare('LIS_address_dms',$this->LIS_address_dms,true);
		$criteria->compare('LIS_folder_path_pdf_dms',$this->LIS_folder_path_pdf_dms,true);
		$criteria->compare('LIS_username_dms',$this->LIS_username_dms,true);
		$criteria->compare('LIS_password_dms',$this->LIS_password_dms,true);
		$criteria->compare('connection_type',$this->connection_type,true);
		$criteria->compare('EHR_address',$this->EHR_address,true);
		$criteria->compare('EHR_directory',$this->EHR_directory,true);
		$criteria->compare('EHR_username',$this->EHR_username,true);
		$criteria->compare('EHR_password',$this->EHR_password,true);
		$criteria->compare('EMR_address',$this->EMR_address,true);
		$criteria->compare('EMR_directory',$this->EMR_directory,true);
		$criteria->compare('EMR_username',$this->EMR_username,true);
		$criteria->compare('EMR_password',$this->EMR_password,true);
		$criteria->compare('PACS_protocol_type',$this->PACS_protocol_type,true);
		$criteria->compare('PACS_protocol_get_type',$this->PACS_protocol_get_type,true);
		$criteria->compare('PACS_protocol',$this->PACS_protocol,true);
		$criteria->compare('PACS_address',$this->PACS_address,true);
		$criteria->compare('PACS_address_local',$this->PACS_address_local,true);
		$criteria->compare('PACS_port',$this->PACS_port);
		$criteria->compare('PACS_port_get',$this->PACS_port_get);
		$criteria->compare('PACS_folder_path',$this->PACS_folder_path,true);
		$criteria->compare('PACS_folder_path_local',$this->PACS_folder_path_local,true);
		$criteria->compare('PACS_folder_path_inbox',$this->PACS_folder_path_inbox,true);
		$criteria->compare('PACS_service_timeout',$this->PACS_service_timeout);
		$criteria->compare('PACS_HL7_extension',$this->PACS_HL7_extension,true);
		$criteria->compare('PACS_username',$this->PACS_username,true);
		$criteria->compare('PACS_password',$this->PACS_password,true);
		$criteria->compare('PACS_connection_type',$this->PACS_connection_type,true);
		$criteria->compare('INV_address',$this->INV_address,true);
		$criteria->compare('INV_directory',$this->INV_directory,true);
		$criteria->compare('INV_api_key',$this->INV_api_key,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Hospital the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return Hospital
	 */
	public static function info(){
		return self::model()->find();
	}
}
