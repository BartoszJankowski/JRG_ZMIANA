<?php
/**
 * Created by PhpStorm.
 * User: Bartosz
 * Date: 23.01.2018
 * Time: 10:52
 */

function test_input($data) {
	if(is_array($data)){
		foreach ($data as $k=>$v){
			$data[$k] = test_input($v);
		}
	} else {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
	}

	return $data;
}

function get_tab_stopnie(){
	return array(
	"STR"=>array("Strażak","str."),
    "ST_STR"=>array("Starszy strażak","st.str."),
    "SEKC"=>array("Sekcyjny","sekc."),
    "ST_SEKC"=>array("Starszy sekcyjny","st.sekc."),
    "ML_OGN"=>array("Młodszy ogniomistrz","mł.ogn."),
    "OGN"=>array("Ogniomistrz","ogn."),
    "ST_OGN"=>array("Starszy ogniomistrz","st.ogn."),
    "ML_ASP"=>array("Młodszy aspirant","mł.asp."),
    "ASP"=>array("Aspirant","asp."),
    "ST_ASP"=>array("Starszy aspirant","st.asp."),
    "ASP_SZTAB"=>array("Aspirant sztabowy","asp.sztab."),
    "ML_KPT"=>array("Młodszy Kapitan","mł.kpt."),
    "KPT"=>array("Kapitan","kpt."),
    "ST_KPT"=>array("Starszy kapitan","st.kpt."),
    "ML_BRYG"=>array("Młodszy brygadier","mł.bryg."),
    "BRYG"=>array("Brygadier","bryg."),
    "ST_BRYG"=>array("Starszy brygadier","st.bryg.")
	);
}

function get_stopien_short(string $name){
	return get_tab_stopnie()[$name][1];
}

function get_tab_funkcje(){
	return array(
			'0'=>array("Stażysta",0,1),
			//1=>array("Młodszy technik",1,2),
			'2'=>array("Młodszy ratownik - kierowca",2,2),
			'3'=>array("Młodszy ratownik",3,2),
			'4'=>array("Młodszy ratownik specjalista",4,4),

			//podoficerskie
			//5=>array("Technik",5,3),
			'6'=>array("Ratownik",6,3),
			'7'=>array("Ratownik kierowca",7,3),
			//8=>array("Starszy technik",8,4),
			'9'=>array("Starszy ratownik",9,4),
			'10'=>array("Starszy ratownik kierowca",10 ,4),
			'11'=>array("Starszy operator sprzętu",11,6),
			'12'=>array("Operator sprzętu specjalnego",12,5),
			'13'=>array("Ratownik specjalista",13,5),
			//14=>array("Dyspozytor",14,4),

			//Aspiranckie
			//15=>array("Młodszy inspektor",15,5),
			//16=>array("Inspektor",16,6),
			//17=>array("Dyżurny stanowiska kierowania",17,7),
			'18'=>array("Starszy operator sprzętu specjalnego",18,7),
			'19'=>array("Starszy ratownik specjalista",19,7),
			'20'=>array("Dowódca zastępu",20,7),
			//21=>array("Starszy insepktor",21,7),
			'22'=>array("Dowódca sekcji",22,8),
			//23=>array("Starszy dyżurny stanowiska kierowania",23,8),
			//24=>array("Starszy inspektor sztabowy",24,8),


			//OFICERSKIE
			//25=>array("Młodszy specjalista",25,8),
			//26=>array("Specjalista",26,9),
			//27=>array("Zastępca dyżurnego operacyjnego",27,9),
			'28'=>array("Zastępca dowódcy zmiany",28,9),
			//29=>array("Dyżurny operacyjny",29,10),
			'30'=>array("Dowódca zmiany",30,10),
			//31=>array("Starszy specjalista",31,10),
			//32=>array("Kierownik sekcji",32,10),
			//33=>array("Oficer operacyjny",33,10),
			//34=>array("Zastępca dowódcy jrg",34,12),
			//35=>array("Dowódca jrg",35,13)
			);

}

function get_nazwa_funkcji($nr){

	return get_tab_funkcje()[$nr][0];

	/*
	DCA_ZMIANY(0,"Dowódca zmiany"),
    PODOFICER(0, "Podoficer dyżurny"),
    KPP(0,"Kierujący pracami podwodnymi"),
    ROTA_MED_1(0,"Rota medyczna"),
    ROTA_MED_2(0,"Rota medyczna"),
    ROTA_MED(0,"Rota medyczna"),
    DCA_ZAST(1,"D-ca."),
    KIEROWCA(1,"Kierowca"),
    RATOWNIK_1(1,"Ratownik"),
    RATOWNIK_2(1,"Ratownik"),
    RATOWNIK_3(1,"Ratownik"),
    RATOWNIK_4(1,"Ratownik"),
    DYSPOZYTOR(1,"Dyspozytor"),
    PRZEJECIE_SPRZETU(0,""),
    PRZEJ_SPRZ(0,"Sprzęt"),
    PRZEJ_POJ(0,"Pojazd");
	*/
}

function get_moth_name(int $num){
	$arr = array('Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień');
	return $arr[$num-1];
}

function get_harmo_types(){
	return array(
		'110'=>array(0=>'Służba, Służba, Wolne',1=>array(1,1,0)),
		'101'=>array(0=>'Służba, Wolne, Służba',1=>array(1,0,1)),
		'011'=>array(0=>'Wolne, Służba, Służba',1=>array(0,1,1))
	);
}

function get_harmo_values(){
	return array(
		'D'=>array('col'=>'w3-cyan','n'=>'Delegacja'),
		'Ch'=>array('col'=>'w3-khaki','n'=>'Chorobowe'),
		'Ud'=>array('col'=>'w3-yellow','n'=>'Urlop dodatkowy'),
		'Uw'=>array('col'=>'w3-green','n'=>'Urlop wypoczynkowy'),
		'O'=>array('col'=>'w3-pale-red','n'=>'Urlop okolicznościowy')
	);
}
function get_grafik_values(){
	return array(
		'Ws'=>array('col'=>'','n'=>'Wolna służba'),
		'D'=>array('col'=>'','n'=>'Delegacja'),
		'Ch'=>array('col'=>'','n'=>'Chorobowe'),
		'Ud'=>array('col'=>'','n'=>'Urlop dodatkowy'),
		'Uw'=>array('col'=>'','n'=>'Urlop wypoczynkowy'),
		'O'=>array('col'=>'','n'=>'Urlop okolicznościowy'),
		'PA'=>array('col'=>'','n'=>'Punkt alarmowy'),
		'PD'=>array('col'=>'','n'=>'Podoficer')
	);
}

function get_harmo_val(string $v){
	return get_harmo_values()[$v];
}
