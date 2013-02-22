package
{
	
	import flash.display.Sprite;
	
	import mx.core.IFlexDisplayObject;
	import mx.managers.PopUpManager;
	
	import spark.components.Group;

	public class Global
	{
		public static var userid:String="";
		public static var username:String="";
		public static var userrole:int=2;
		public static var barx:int=1;
		public static var bary:int=1;
		public static var barw:int=400;
		public static var barh:int=250;
		
		public static var loading:Group;
		
		public static function showloading(src:Sprite):void{
			if(!loading){
				loading=new Group();
				loading.width=200;
				loading.height=100;
			}
			
			PopUpManager.addPopUp(loading,src,true);
			PopUpManager.centerPopUp(loading);
		}
		
		public static function hideloading():void{
			PopUpManager.removePopUp(loading);
		}
		
		public static function showPopUp(child:IFlexDisplayObject,par:Sprite):void{
			PopUpManager.addPopUp(child,par,true);
			PopUpManager.centerPopUp(child);
		}
		public function Global()
		{
		}
	}
}