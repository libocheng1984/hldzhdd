function MapControl(){
var p = this;
	this.ljfxClick = function(feature,evt){
		var jqid = feature.trafficInfo.jqid;
		var event_x = feature.geometry.x;
		var event_y = feature.geometry.y;
		var group_x = "";
		var group_y = "";
		
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"jqid":jqid}}
				};
		var Loader=new AJAXObj();
		//Loader.isasync=false;
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var values = backJson['value'];
			var hphm = values['records']['hphm'];
			for(var i=0;i<mainWindow.stationGroupStore.length;i++){
				if(mainWindow.stationGroupStore[i]['hphm']==hphm){
				 	var groupMarker = mainWindow.stationGroupStore[i]['location'];
				 	var point = OpenLayers.Geometry.fromWKT(groupMarker);
				 	group_x = point.x;
				 	group_y = point.y;
				 	//p.findCameraData(event_x,event_y,group_x,group_y);
					console.log('警情点:'+event_x+','+event_y+';警车:'+group_x+','+group_y);
				 	p.onPathAnalysis(event_x,event_y,group_x,group_y,feature);
				 	break;
				}
			}
		});
		Loader.POSTDATA("php/event/GetHPHMByEventId_web.php",postData);
	
	}
	
	/*
	this.findCameraData = function(event_x,event_y,group_x,group_y){
		var cameralayer = mapObj.getLayersByName("摄像头")[0];
		var postData={
					"event":"Camera",
					"extend":{"eventswitch":"Camera"},
					"content":{"condition":{"event_x":event_x,"event_y":event_y,"group_x":group_x,"group_y":group_y}}
				};	
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.jwd);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:16,   
						graphicHeight : 16,   
						graphicXOffset : -8,   
						graphicYOffset : -8, 
						externalGraphic : "images/zhdd/cam.png"
					};
				features.push(f);
			}
			cameralayer.removeAllFeatures();
			cameralayer.addFeatures(features);
			cameralayer.setVisibility(true);	
		})

		Loader.POSTDATA("php/layer/GetCamera_LJFX.php",postData);
	}
	*/
	this.findCameraData = function(Linefeature){
		var cameralayer = mapObj.getLayersByName("摄像头")[0];
		var loadLjfxCamera=function(){
			var cameraFeatures = mainWindow.cameraFeatures;
			var radius = 39.3701/4374754*100;
			var lxfxFeatures = new Array();
			for(var i=0;i<cameraFeatures.length;i++){
				var camersFeature = cameraFeatures[i];
				var minter =camersFeature.geometry.distanceTo(Linefeature.geometry);
				if(minter<radius){
					lxfxFeatures.push(cameraFeatures[i]);
				}
			}
			cameralayer.removeAllFeatures();
			cameralayer.addFeatures(lxfxFeatures); 
			cameralayer.setVisibility(true);
		}
		var cameraFeatures = mainWindow.cameraFeatures;
		if(cameraFeatures.length>0){
			loadLjfxCamera();
		}else{
			$(document).one("loadLjfxCamera",loadLjfxCamera);
			cameralayer.setVisibility(true);
		}
	}
	
	this.onPathAnalysis = function(event_x,event_y,group_x,group_y,ft) {
		var ljfxLayer = mapObj.getLayersByName("路径分析")[0];
		ljfxLayer.removeAllFeatures();
		/*
		var lineStr = "LINESTRING(121.614648581163 38.9107016976856,121.615856309915 38.9109577284998,121.615929447507 38.9109765415887,121.615929447507 38.9109765415887,121.616144903326 38.9103886588908,121.616144903326 38.9103886588908,121.616439855753 38.9096551712792,121.616532522646 38.9094352548379,121.616532522646 38.9094352548379,121.616706587555 38.9088076832119,121.617064187347 38.9079372400628,121.617064187347 38.9079372400628,121.6173399331 38.9079369864995,121.61811157794 38.9077666405916,121.618182850949 38.907767484029,121.618566933327 38.9077720284706,121.618752117546 38.9077985702926,121.618752117546 38.9077985702926,121.619131719734 38.9078529764021,121.619555099364 38.907873454735,121.619733210982 38.9078755602061,121.619733210982 38.9078755602061,121.62018071942 38.9078808490382,121.620621917832 38.9078787750196,121.620621917832 38.9078787750196,121.621641899551 38.9078739738269,121.6221692162 38.9078726745069,121.6221692162 38.9078726745069,121.622229601625 38.9078725255638,121.622229601625 38.9078725255638,121.623825750309 38.9078685772707,121.624114829465 38.9078613178173,121.624114829465 38.9078613178173,121.626193670275 38.9078090921169,121.626574122053 38.907759106991,121.627241410317 38.907694339509,121.627665292736 38.9075697818817,121.627665292736 38.9075697818817,121.627974035529 38.9074790567191,121.628062556689 38.9074589974026,121.628106883041 38.9074384666856,121.628106883041 38.9074384666856,121.628337966241 38.9073314349912,121.62880371634 38.9071756963723,121.62880371634 38.9071756963723,121.628988493343 38.9071139094906,121.629578055825 38.9070693636966,121.630206861012 38.907124991256,121.630383473989 38.9071463627494,121.630566663659 38.907124085064,121.630566663659 38.907124085064,121.63069708552 38.9071082242845,121.631002390572 38.907000000219,121.631255913563 38.9068721915899,121.631529516521 38.9067043775447,121.631704258991 38.9066561250177,121.631778505611 38.9066348994125,121.631778505611 38.9066348994125,121.632008496964 38.9065691491588,121.632323869973 38.9065728433282,121.632715223418 38.9066428166187,121.63350022895 38.9068804947399,121.63350022895 38.9068804947399,121.633752104159 38.9069567541922,121.634156061637 38.9071505612664,121.634244325304 38.9071948099688,121.634244325304 38.9071948099688,121.634332225658 38.9072388764142,121.634332225658 38.9072388764142,121.634331716166 38.907228092701,121.634434288197 38.9072538670849,121.634697195152 38.907472710016,121.634697195152 38.907472710016,121.635143427479 38.9078441480356,121.635143427479 38.9078441480356,121.635157447872 38.9078558183398,121.635329693887 38.9079648554312,121.635403367467 38.9080192283983,121.635607018768 38.908103027835,121.635607018768 38.908103027835,121.635804908903 38.9081844561141,121.63639547667 38.9082203305474,121.636945439423 38.9082186950218,121.636945439423 38.9082186950218,121.636991166218 38.9082185589189,121.636991166218 38.9082185589189,121.640306759985 38.9079190681897,121.640306759985 38.9079190681897,121.645453545756 38.9074539775697,121.645453545756 38.9074539775697,121.652499490362 38.9068168900491,121.652638874381 38.9068074046845,121.652679805939 38.906807876955,121.652679805939 38.906807876955,121.653086146647 38.9068125645567,121.653222441914 38.9068119519301,121.653222441914 38.9068119519301,121.653451637875 38.9068109213699,121.653451637875 38.9068109213699,121.653717082592 38.9068097272576,121.653961011237 38.9068829495499,121.653961011237 38.9068829495499,121.65398707465 38.9068770078164,121.654178548756 38.9068850481871,121.654747107421 38.9068969417479,121.654747107421 38.9068969417479,121.655536433346 38.9069134488979,121.655536433346 38.9069134488979,121.656018103929 38.9069235194359,121.657917182682 38.906975252146,121.657917182682 38.906975252146,121.658201547112 38.9068986927426,121.658260966273 38.906903949658,121.658260966273 38.906903949658,121.658833385533 38.9069545909884,121.659475521867 38.90701465735,121.660181649315 38.9069822281651,121.660503822206 38.9069534976833,121.660743010725 38.9069238146094,121.661088921032 38.906777850908,121.661180767943 38.9067344494449,121.661269884838 38.9066718858909,121.661269884838 38.9066718858909,121.661953339017 38.9061920682425,121.662460906803 38.9058244649225,121.662806366485 38.9056061485967,121.663104651938 38.9054762002529,121.663293866875 38.9054097618638,121.663293866875 38.9054097618638,121.663975166853 38.9051705357459,121.66458160667 38.904990763296,121.664937100526 38.904843682602,121.665073935135 38.9047353344437,121.665073935135 38.9047353344437,121.665548480629 38.9048190780455,121.666433750339 38.9048610224477,121.668448373473 38.9049635902666,121.66921145857 38.9050041203293,121.669506451102 38.9050233974132,121.669669517953 38.9050172973533,121.669761307993 38.9050103851484,121.670252956822 38.9048608065134,121.670816695959 38.904668278167,121.671226955787 38.9045137900451,121.671513395385 38.9044454283226,121.671666278496 38.9044392096088,121.672469775094 38.904453888142,121.672469775094 38.904453888142,121.672512509505 38.9042795302233,121.672527043287 38.9040316790878,121.672602665442 38.903699266714,121.672799813559 38.9032326015312,121.673123603213 38.9023565977691,121.673309890152 38.9019401865135,121.673431341098 38.9018098074698,121.673692921829 38.90161126525,121.674235984123 38.9012105296765,121.674289487488 38.9011690550297,121.674289487488 38.9011690550297,121.674708976533 38.9008438736633,121.675044416021 38.900593056537,121.675049258473 38.9005899626798,121.675049258473 38.9005899626798,121.675053126512 38.9005874913773,121.675548202009 38.9001988341843,121.675810691176 38.8999849472206,121.675903788216 38.8998849978275,121.67591227687 38.8996325815896,121.675958850555 38.8983527226444,121.675964581452 38.8982488118653,121.675874795611 38.8981705547415,121.675746229696 38.8981334481117,121.675415558874 38.8981296980096,121.674947893291 38.8981333048722,121.674687733702 38.8980516046798,121.674687733702 38.8980516046798,121.674877664671 38.8980255615313,121.675058020002 38.8977305342877,121.675671456504 38.8968373592395,121.67575324074 38.8967300696922,121.67575324074 38.8967300696922,121.676203599732 38.8976095025375,121.676459913614 38.8981784645146,121.676568460661 38.8984925155163,121.676789604369 38.8995874115709,121.676843764199 38.8997450701355,121.676937039821 38.8998579125165,121.677077714057 38.8999861965189,121.677238149807 38.9000774423283,121.677598682456 38.9001784063145,121.677930334874 38.9002939469883,121.678110256827 38.9003630550885,121.678157103185 38.900408299587,121.678305934728 38.9006111981734,121.67833163976 38.9007679889106,121.678337584214 38.9009618178597,121.678205962599 38.901373934779,121.67808352927 38.9018047864492,121.678059555077 38.9020690742191,121.678095485924 38.9021887188756,121.678234509341 38.9024064112308,121.678328481107 38.9024819985149,121.678546412065 38.9025589884935,121.678915666164 38.9027047613698,121.679815804329 38.9028782794737,121.68118242554 38.9030892787667,121.681648118858 38.9031640544633,121.681648118858 38.9031640544633,121.682045218539 38.9031222840339,121.68265797049 38.9030099046643,121.683435267949 38.9028902008603,121.683859139572 38.9028307439894,121.684387835513 38.9028183485731,121.68472074138 38.9027676933386,121.684790170532 38.9027340647444,121.684790170532 38.9027340647444,121.685602966738 38.9032756268436,121.685708245354 38.9033408131965,121.685748247247 38.9033924645465,121.685763328041 38.9034630358536,121.685779116533 38.9034952142582,121.685750656924 38.9037060987224,121.685729088825 38.903987462311,121.685758778824 38.9041542001815,121.68582004633 38.9043852948402,121.685856156777 38.9046481070758,121.685928326804 38.9047321210692,121.686066836588 38.9047720804647,121.686107547044 38.9047853388011,121.686164871872 38.904785983763,121.686238928952 38.9047676165087,121.686313103898 38.9047428503901,121.686371136078 38.9047051023489,121.686404954133 38.9046478814739,121.686480543537 38.904546329438,121.686597432467 38.9044260414233,121.686787316725 38.9043449750328,121.686861608897 38.9043138097343,121.686911215541 38.9042887669625,121.686927829644 38.9042761534878,121.686946682762 38.9041419624504,121.686957464468 38.9040012805085,121.687050844696 38.9038231263872,121.687142516009 38.9037377550683,121.687200311292 38.9037128042269,121.687405746638 38.9036767126395,121.687601814831 38.9037045168671,121.687863278594 38.9037394557214,121.688181007117 38.903832627355,121.688457319815 38.9039509335719,121.688643787765 38.9040554299747,121.688820125433 38.9042654147318,121.688986451146 38.9045744890663,121.689155954749 38.9047107949255,121.689253167158 38.9047694874082,121.689334706962 38.9047896030405,121.689375653323 38.9047900625987,121.68942525945 38.9047650187508,121.689491361693 38.9047337598614,121.689557934321 38.9046769056329,121.689592808294 38.9045620943618,121.689618904895 38.9044791853117,121.689685712376 38.9044095333506,121.689768545399 38.904359261591,121.689917244786 38.9042905282794,121.689991300448 38.9042721586172,121.690253707982 38.9042559015532,121.690514940183 38.9043036321775,121.690833908567 38.9043296089858,121.691104622867 38.9043070429867,121.691539238868 38.9042799127902,121.692299956343 38.904336434994,121.69261874966 38.9043720051879,121.692733633601 38.9043604910112,121.692848634823 38.9043425778869,121.692922807143 38.9043178075186,121.693029501728 38.9043062013876,121.693168718621 38.9043077594052,121.693267106652 38.9043024602473,121.693423874631 38.9042402128787,121.693681257698 38.9040510878955,121.693789358514 38.9039626950211,121.693840720904 38.9038416667028,121.693842948129 38.9037200887983,121.693732111726 38.9036001893391,121.693453029598 38.9034485328498,121.693078179894 38.9031575133177,121.692547129156 38.902805842306,121.691826362225 38.9024341172679,121.691631919733 38.9023141358075,121.6915933508 38.9022727286791,121.691607397336 38.9022216672966,121.691620974029 38.902196210028,121.691680330772 38.9021763875291,121.691798480453 38.9021674673807,121.692028038436 38.9021597949715,121.692191968148 38.9021565090414,121.692433693879 38.9022001908621,121.692628325238 38.9023099293567,121.692830533972 38.9023639215491,121.692996524518 38.9023726125111,121.693128169427 38.9023467535066,121.69332113996 38.9023147474795,121.693521977025 38.9023306606701,121.693652620376 38.902359454474,121.693817484715 38.9024296293535,121.693956120517 38.9024995106694,121.694077771159 38.9025418695783,121.694260622716 38.9025849126765,121.694765738947 38.9026349743846,121.694842821343 38.902630366304,121.694948097469 38.9026151340588,121.695018381615 38.9025995106983,121.695173947308 38.9025137354656,121.695309017821 38.9024003830684,121.695548463025 38.902321014013,121.69569712916 38.9022296914059,121.69583899661 38.9021273534881,121.695881686787 38.9020895430542,121.696036150298 38.902063919905,121.69610603368 38.9020701697945,121.696196712111 38.9020875910047,121.696315183994 38.9021162616149,121.696427457424 38.9021011061417,121.696617412286 38.9020485304085,121.696975889857 38.9020100929906,121.697222764552 38.9019836760967,121.69743274081 38.9019335101348,121.697635997669 38.9018424291293,121.698679299473 38.9011335300065,121.699033764247 38.9009391149047,121.699200224364 38.9008184499602,121.699261432284 38.9007374522417,121.699354414237 38.9005517918942,121.69943118957 38.9004359619656,121.699536334286 38.9004021273904,121.699745347089 38.9004044547303,121.700200749655 38.9004066071786,121.700395151959 38.9003912679888,121.700485047792 38.9003747656068,121.700732765958 38.9003016766253,121.701039776439 38.9002525836324,121.701294320962 38.9002145748812,121.701532979847 38.9002288971992,121.701815471512 38.9002962145482,121.702105630242 38.9003525286803,121.702533145881 38.9003961964722,121.702959671264 38.9004943358611,121.703335272187 38.9006541758895,121.703483096815 38.9007414350982,121.703602460305 38.9007505436644,121.703652395521 38.9007433145331,121.703672878895 38.9007124081029,121.703693786652 38.9006581560253,121.703705160044 38.9005804477565,121.703679952302 38.9003233140382,121.703702133107 38.900199025018,121.703763582623 38.9001063056716,121.703853512419 38.9000177941166,121.704023404186 38.8998826122712,121.704179276662 38.8996413589572,121.704213652595 38.8995046730614,121.704198615252 38.899454663547,121.704159210903 38.8994293049861,121.704056374843 38.8993845515285,121.703756631437 38.8992067755687,121.703608062554 38.899046252933,121.703052290754 38.8984793521089,121.702843706723 38.8981995354938,121.70273915938 38.8979839771815,121.702621491557 38.8977882169256,121.7022578947 38.8974326656846,121.701876570552 38.8969996336089,121.701733435707 38.8968010963807,121.701577588895 38.8965999246554,121.701541674236 38.8964698899186,121.701549142389 38.8964101412127,121.701575384633 38.8963705451509,121.701634248071 38.8962914239263,121.70177034032 38.8961757663571,121.701810338529 38.8960814773898,121.701818985999 38.8959569240472,121.701809676032 38.8957673533636,121.701743409036 38.8955522196296,121.701620232478 38.8954257050482,121.701310555299 38.8951564948585,121.701210123227 38.8950690037394,121.701168589112 38.8950153884426,121.701081133974 38.8946822071085,121.70099574882 38.8943603743941,121.70099574882 38.8943603743941,121.701126237021 38.8943505108427,121.701318703927 38.8942070624098,121.70157253492 38.893940003475,121.701841737914 38.8937107726876,121.702068393411 38.89361287091,121.702229555844 38.893583280339,121.702486045654 38.8936112354528,121.702566683879 38.8935933023849,121.702784167225 38.8935580604077,121.703132975855 38.8933673686396,121.703596192011 38.893068110405,121.703816883638 38.8928656335273,121.703816883638 38.8928656335273,121.703821050536 38.8928741222205,121.703832909588 38.8928982811544,121.703806092017 38.8930205228357,121.70378616431 38.8931019945398,121.703690541712 38.8932883462677,121.703533178159 38.8934884289278,121.703314640785 38.8936710130551,121.703090609614 38.8938174947856,121.70293551484 38.8938926597816,121.702743445041 38.8939722193256,121.702471452215 38.8940508906216,121.702274107751 38.8940823361644,121.702205781544 38.8941200206099,121.70216204687 38.8941579782981,121.702142379214 38.8942250361979,121.702146909756 38.8943139875863,121.702205941101 38.8944491965077,121.702283329061 38.8945894148354,121.702452412147 38.8947594846803,121.702621146455 38.894948772251,121.702827075412 38.8951216535964,121.702911224933 38.8952283083774,121.702922122907 38.895305316794,121.702926436013 38.8954062793806,121.702865511561 38.8957131522171,121.702861928912 38.8959101363155,121.702891183718 38.8959921540743,121.702921312374 38.8960261269246,121.702970234761 38.8960410866499,121.703031802787 38.8960369649491,121.703081162045 38.8960279021764,121.70318040469 38.8959809496239,121.703330744678 38.8958293225345,121.703554414793 38.89570999772,121.703644987124 38.8956917702865,121.704014080407 38.8956958667176,121.704309355089 38.8956991430227,121.704415516326 38.8957259645724,121.704419192805 38.8957269677875,121.704419192805 38.8957269677875,121.704632732608 38.8955324011685,121.705028533955 38.8951772026755,121.705255504997 38.8950229757082,121.705387269911 38.8949137939716,121.705449094688 38.8947577358137,121.705454448827 38.8944627485956,121.705507488603 38.8941406293084,121.705677315795 38.893884345795,121.706072935427 38.8935383617711,121.706373098145 38.8932512504134,121.706610685772 38.8931616797612,121.706728643652 38.8931629860371,121.70690424352 38.8932386920926,121.707126358656 38.8933517936608,121.707357597322 38.8936125191229,121.707462423706 38.8936874409843,121.707530359248 38.893844936368,121.707538814297 38.8940294340947,121.707489291853 38.8941579689397,121.707440270398 38.8942588486828,121.707273455991 38.8943492044034,121.707083216267 38.8944300803437,121.706950786321 38.8945761374877,121.70679259704 38.8950068974219,121.706656803272 38.8951782132182,121.706477624117 38.8953029631134,121.706121144689 38.8954487911601,121.705423758073 38.8956945313662,121.705066438014 38.8958864320385,121.704789806498 38.8960380514385,121.704789806498 38.8960380514385,121.704795077556 38.8960571329815,121.704810201373 38.8961278211294,121.704748476181 38.8962281577132,121.704522415578 38.8965246903172,121.704293939795 38.8968364184097,121.704151894893 38.8971767092528,121.704114819645 38.8974226118743,121.704133853007 38.897571460836,121.704261709579 38.8977130236601,121.704326139473 38.8977562064295,121.7044019763 38.8977697881596,121.704461975614 38.8977577133912,121.704527871129 38.8977202232382,121.704594067397 38.8976661719461,121.704896100148 38.8974032371367,121.705234168378 38.8971520323039,121.705401916279 38.8970972356445,121.7055106464 38.8970984408973,121.705698495144 38.8971345167149,121.705994611834 38.897197287254,121.706326921157 38.8972632907845,121.706528754494 38.8973278483395,121.706708636633 38.8974034937225,121.706802253562 38.8974385241986,121.706896589545 38.89743390314,121.70695509277 38.8974062227434,121.707051174535 38.8973053051475,121.70722138584 38.8971145579704,121.707484545593 38.8969956596751,121.708242511436 38.8967774206291,121.708605867113 38.8967304484098,121.708881315698 38.8967334939383,121.709032614475 38.8967861572137,121.709089783448 38.8968321142202,121.709131532407 38.8969288914569,121.709164443631 38.8971133883078,121.709168616508 38.8972834034207,121.709159624824 38.8973796197939,121.709129604659 38.897435944302,121.709063135746 38.8975031971704,121.708989520517 38.8975647053676,121.708850461408 38.8976368212676,121.708396884724 38.8978612638468,121.70759999477 38.898226379458,121.707086982184 38.8985294779435,121.706926172809 38.8986013507178,121.706772714676 38.8986676390425,121.706627121512 38.8987000204849,121.706540135423 38.8986990570893,121.706286631541 38.8986849178317,121.70608428074 38.898648682268,121.705924806394 38.8986469152538,121.705836792422 38.8987025962448,121.705785536543 38.8987303563821,121.705763276001 38.8987584378355,121.705754896312 38.8988206668977,121.705715156599 38.8990128578916,121.705690942544 38.8991485646411,121.705615626247 38.8993035346978,121.70557866201 38.8993427843993,121.705576914063 38.8994390807225,121.705604984349 38.8994903825121,121.705640714863 38.8995191066801,121.705776799492 38.8996112649524,121.706035499764 38.8997387754952,121.706214873514 38.8998427441558,121.706542263516 38.9001806428186,121.706585141004 38.9002151114855,121.706621129128 38.9002296741351,121.706807240349 38.9003620446522,121.706921786478 38.9004426318286,121.706913818432 38.9004822030042,121.706818142972 38.900560462392,121.706813431647 38.9005609754864,121.706813431647 38.9005609754864,121.706723700108 38.900570747805,121.70654350181 38.9005120958205,121.70629806265 38.9004527208471,121.705871295832 38.9003970017412,121.705719169412 38.9003896502205,121.705558765765 38.9004388630385,121.705397693449 38.9005248948047,121.705367566102 38.90058688273,121.705366332002 38.9006548565852,121.705387256389 38.9007004135723,121.705581026824 38.9008102084244,121.705804822198 38.900863679421,121.706058128117 38.9008891488292,121.706311845302 38.9008919597316,121.70657893044 38.9009572400516,121.706680006473 38.9009810219898,121.706766173497 38.9010273012539,121.706851107927 38.9011415543454,121.706928279925 38.9012840495658,121.70694082647 38.9013918354214,121.706932190631 38.9014682257795,121.706908183367 38.9015926037701,121.70694952107 38.9017120396689,121.707013838971 38.9017637424544,121.707085406123 38.9018155254631,121.707150135034 38.9018445702134,121.707251212522 38.9018683516782,121.707403239623 38.9018813657482,121.707555472129 38.901883050638,121.70767084283 38.9019183211814,121.707756908977 38.9019702642292,121.707842051398 38.902073187644,121.707884006816 38.9021586362815,121.707896554926 38.9022664220459,121.707842987569 38.9024216341061,121.707826436064 38.9025347635155,121.707881658414 38.9026883464743,121.707953021834 38.9027514579529,121.708010502692 38.9027804220368,121.708053895615 38.902786567729,121.708097391179 38.9027870489133,121.708162942409 38.9027707771831,121.708250344005 38.9027490814848,121.708351525773 38.9027671975289,121.708451784154 38.9028362939205,121.708471890303 38.9029271663237,121.708485362831 38.9029839715953,121.708499245786 38.9030181188946,121.708585416454 38.9030643968622,121.708628809605 38.9030705423443,121.708679862406 38.9030541099991,121.708709064735 38.9030431016507,121.708782378255 38.9029985872673,121.708884688804 38.9029543934293,121.709051627367 38.9029449077137,121.70910196211 38.9029681266381,121.709130138828 38.9030137631228,121.709157392636 38.9031103800471,121.709125831642 38.9032516718667,121.709160437397 38.903342704427,121.709200730206 38.9033872237133,121.709248265871 38.903433100075,121.709325499498 38.9034415121918,121.709403827438 38.9033894684201,121.709452457487 38.9033748888622,121.709617143045 38.9033615917171,121.709674350266 38.9034075747742,121.709663174616 38.9034905946989,121.709632246002 38.9035960718354,121.709621343797 38.903663977792,121.70963019455 38.9037094265369,121.709648853423 38.9037474251644,121.709686991778 38.9037780805302,121.709764362589 38.9037789353831,121.709890363636 38.9037652104485,121.709968828264 38.9037056093185,121.710085841384 38.9036539924266,121.710183238269 38.9036172757428,121.71025080089 38.9036255804336,121.710269870164 38.9036409080216,121.710795508185 38.9039944026627,121.711293707924 38.9042606695054,121.711379658081 38.9043220858995,121.711446128931 38.9043908457873,121.711472958102 38.9045120776831,121.711459462567 38.9047235665295,121.711414903679 38.9050480899668,121.711368636995 38.90546707565,121.711362490476 38.9058071398712,121.711327260519 38.9061506623781,121.711293874289 38.9063921655996,121.711291278868 38.9065357482462,121.711298628383 38.9066643236603,121.711315649684 38.9067930058038,121.711379254235 38.9070204623398,121.711388920891 38.9071216105251,121.711489724138 38.907249583085,121.711581277761 38.9074727348778,121.711581277761 38.9074727348778,121.710974946936 38.9077657935986,121.710174228129 38.9078849407527,121.709405517796 38.9079324425945,121.708698946714 38.9079406300949,121.708103666759 38.908022038134,121.707618371523 38.9082486474886,121.707213801212 38.9085401427621,121.706748101284 38.9088149610599,121.705191963306 38.9092573019749,121.705191963306 38.9092573019749,121.704874908674 38.908587471494,121.704672876416 38.9082060272739,121.704387035434 38.9077999526658,121.704127695784 38.9076048399926,121.703877604866 38.9074572298231,121.703600940937 38.9072882572666,121.703416616704 38.9072309103418,121.70291755976 38.9070594667038,121.702368009004 38.9068848265036,121.701536900886 38.9066254193183,121.700937148987 38.906431780705,121.70046025776 38.9062777001855,121.700205306508 38.9062158897866,121.699979156047 38.9062007353305,121.699739146601 38.9062064878823,121.699498599362 38.9062417204119,121.69924946741 38.9063042371057,121.698856572825 38.9064676427219,121.698856572825 38.9064676427219,121.699225849345 38.9068600043901,121.699320219696 38.9070063816545,121.699426215748 38.9071065524608,121.699881122733 38.9075855074961,121.70009542952 38.9078069355145,121.700502371208 38.9082558677296,121.700676639275 38.9084557873619,121.700788613919 38.9086718630894,121.700923195168 38.9089787557281,121.701146735523 38.9095815022042,121.701473415158 38.9103285927429,121.701473415158 38.9103285927429,121.698780137029 38.9112062212136,121.697860396115 38.9115929989078,121.697860396115 38.9115929989078,121.695977886809 38.912384612481,121.695653775184 38.9124758254166,121.695447622754 38.9124297055749,121.694992730363 38.9122456063929,121.693492541415 38.9117339036408,121.693268980359 38.9116752401277,121.693017310602 38.9116794438039,121.69287247307 38.9116931397455,121.69287247307 38.9116931397455,121.692486247719 38.9117296605866,121.691262994625 38.9117966900313,121.690633367877 38.9118317531644,121.690290700675 38.9118981127655,121.690145001645 38.9119396164161,121.690145001645 38.9119396164161,121.690101012626 38.9119521470296,121.689992177054 38.9120070880598,121.689865245014 38.9120688461968,121.689638331266 38.9121926644799,121.689432776929 38.9122949632964,121.689021621145 38.9125148674406,121.68867118051 38.9126886770793,121.688321082428 38.9128437795483,121.687694605156 38.9130519050833,121.687537695861 38.9130762494546)";
		var line = OpenLayers.Geometry.fromWKT(lineStr);
		var feature = new OpenLayers.Feature.Vector(line);
		ljfxLayer.addFeatures([feature]);
		mainWindow.mapObj.zoomToExtent(feature.geometry.getBounds(),true); 
		p.findCameraData(feature);
		*/
		var url = "http://10.80.8.204:8090/JCDL/NetAnalysis/PathAnalysis.ashx?stops="+event_x+","+event_y+";"+group_x+","+group_y+"&&barriers=";
			console.log(url);
			var postData={
				"event":"TRANS",
				"extend":{"eventswitch":"search"},
				"content":{"condition":{"url":url}}
			};
			//console.log(url);
			var Loader = new AJAXObj();
			$(Loader).bind("JSON_LOADED",function(e,backJson){
				//alert(JSON.stringify(backJson));
				var value = backJson['value'];
				var status = $(value).find("Status").text();
				if(status!="Success"){
					return;
				}
				//alert(value);
				var path = $(value).find("points");					
				var wkt = "LINESTRING(";
				var lineStr = "";
				for (var i=0;i<path.length;i++) {
					var point = $(path[i]).find("Point");
					var pointStr = "";
					for(var j=0;j<point.length;j++){
						var x = $(point[j]).find("x").text();
						var y = $(point[j]).find("y").text();
						//console.log("x:",x,";y:",y);
						if(j!=0){
							pointStr +=",";
						}
						pointStr += x+" "+y;
					}
					if(i!=0){
						lineStr +=",";
					}
					lineStr += pointStr;
					/*	
					var pointStr = path[i].innerHTML;
					var regS = new RegExp("</x><y>","g");
					pointStr = pointStr.replace(regS," ");
					var regS = new RegExp("</y></point><point><x>","g");
					pointStr = pointStr.replace(regS,",");
					var regS = new RegExp("<point><x>","g");
					pointStr = pointStr.replace(regS,"");
					var regS = new RegExp("</y></point>","g");
					pointStr = pointStr.replace(regS,"");
					if(i!=0){
						lineStr +=",";
					}
					lineStr += pointStr;
					*/
				}

				wkt += lineStr + ")";
				//alert(wkt);

				var line = OpenLayers.Geometry.fromWKT(wkt);
				var feature = new OpenLayers.Feature.Vector(line);
				ljfxLayer.addFeatures([feature]);
				//mainWindow.mapObj.zoomToExtent(feature.geometry.getBounds(),true); 
				p.findCameraData(feature);
				
				//var polygon = OpenLayers.Geometry.fromWKT(wkt);
				//var ft = new OpenLayers.Feature.Vector(polygon);
				//p.orgVectorLayer.addFeatures([ft]);

				//p.currnetOrgCode = orgcode[0].innerHTML;

				mainWindow.mapObj.removePopup(ft.popup);
			   	ft.popup.destroy();
             	ft.popup = null;
			});
			$(Loader).bind("SYS_ERROR",function(e,msg){
				$.error("调用失败!");
			});
			Loader.POSTDATA("php/trans.php",postData);
	}
}