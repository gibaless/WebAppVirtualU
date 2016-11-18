<?
/*
**********************************************************************
' Nombre: mod_shopping_cart.inc
' Objetivo: Tiene todas las funciones que se necesitan para el CARRO DE COMPRAS.
'----------------------------------------------------------------------
' Autor: Gerardo J. Ruano
' Fecha de creación: 12/10/2002
**********************************************************************
*/
	// FUNCION que devuelve la cantidad ITEMs del SHOPPING CART
	function GetCountItemShoppingCart()
	{
		$iCantItems = 0;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != ""){
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			$iCantItems = count($ArrayItems);
		}
		
		return $iCantItems;
	}
	
	// FUNCION que devuelve un ITEM específico del SHOPPING CART
	function GetItemShoppingCart($Position)
	{
		$strResult = "";
		$Pos = $Position - 1;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			$iCantItems = count($ArrayItems);

			if($Pos < $iCantItems && $Pos >= 0)
				$strResult = $ArrayItems[$Pos];
		}
		
		return $strResult;
	}
	
	// FUNCION que devuelve el Codigo de Producto de un ITEM específico del SHOPPING CART
	function GetProductoShoppingCart($Item)
	{
		$strResult = "";
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			$ArrayItems = split('\|', $Item);
			$strResult = $ArrayItems[0];
		}
		
		return $strResult;
	}
	
	// FUNCION que devuelve el Codigo de Variante de un ITEM específico del SHOPPING CART
	function GetImageShoppingCart($Item)
	{
		$strResult = "";
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			$ArrayItems = split('\|', $Item);
			$strResult = $ArrayItems[1];
		}
		
		return $strResult;
	}
	
	// FUNCION que devuelve la cantidad de un ITEM específico del SHOPPING CART
	function GetCantidadShoppingCart($Item)
	{
		$strResult = "";
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			$ArrayItems = split('\|', $Item);
			$strResult = $ArrayItems[2];
		}
		
		return $strResult;
	}
	
	// PROCEDIMIENTO que Setea la Cantidad de un ITEM del SHOPPING CART
	function SetCantidadShoppingCart($Item, $NuevaCantidad)
	{
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			foreach($ArrayItems as $ValorItem)
			{
				if($Item == $ValorItem)
				{
					// Obtengo un Array de los ARTICULOS de cada ITEM del SHOPPING CART
					$ArrayArticulos = split('\|', $ValorItem);
					// Si el Item Existe, Seteo la cantidad actual con la ingresada
					// Armo el Item Actual que estoy examinado
					$strItemModificado = $ArrayArticulos[0] . "|" . $ArrayArticulos[1] . "|" . $NuevaCantidad;
					// Busco donde tengo que reemplazar al ITEM Actual con el ITEM Ingresado
					$_SESSION["ShoppingCart"] = str_replace($Item, $strItemModificado, $_SESSION["ShoppingCart"]);
					
					break;
				}
			}
		}
	}
	
	// PROCEDIMIENTO que Setea la Imagen de un ITEM del SHOPPING CART
	function SetImageShoppingCart($Item, $NuevaImagen)
	{
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			foreach($ArrayItems as $ValorItem)
			{
				if($Item == $ValorItem)
				{
					// Obtengo un Array de los ARTICULOS de cada ITEM del SHOPPING CART
					$ArrayArticulos = split('\|', $ValorItem);
					// Si el Item Existe, Seteo la cantidad actual con la ingresada
					// Armo el Item Actual que estoy examinado
					$strItemModificado = $ArrayArticulos[0] . "|" . $NuevaImagen . "|" . $ArrayArticulos[2];
					// Busco donde tengo que reemplazar al ITEM Actual con el ITEM Ingresado
					$_SESSION["ShoppingCart"] = str_replace($Item, $strItemModificado, $_SESSION["ShoppingCart"]);
					
					break;
				}
			}
		}
	}
			
	// PROCEDIMIENTO que AGREGA un ITEM al SHOPPING CART
	function AddItemShoppingCart($CodigoArticulo, $Imagen, $Cantidad)
	{
		static $ArrayItems;
		static $ArrayArticulos;
		static $bExisteItem;
		static $strItemActual;
		static $strItemIngresado;
		static $iPosicionItem;
		static $iCantidadTotal;
		
		$bExisteItem = false;
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			foreach($ArrayItems as $Item)
			{
				// Obtengo un Array de los ARTICULOS de cada ITEM del SHOPPING CART
				$ArrayArticulos = split('\|', $Item);
				
				// Si el Item Existe, Sumo la cantidad actual con la ingresada
				if( (trim("".$ArrayArticulos[0]) == trim("".$CodigoArticulo)) )
				{
					// Armo el Item Actual que estoy examinado
					$strItemActual = $ArrayArticulos[0] . "|" . $ArrayArticulos[1] . "|" . $ArrayArticulos[2];
					// Obtengo la cantidad total para el caso de que lo encontré
					$iCantidadTotal = intval($ArrayArticulos[2]) + intval($Cantidad);
					// Armo el Item Ingresado
					$strItemIngresado = $CodigoArticulo . "|" . $Imagen . "|" . $iCantidadTotal;
					
					// Busco donde tengo que reemplazar al ITEM Actual con el ITEM Ingresado
					$_SESSION["ShoppingCart"] = str_replace($strItemActual, $strItemIngresado, $_SESSION["ShoppingCart"]);
					$bExisteItem = true;
					break;
				}
			}
		}
		
		// Si no existe, lo agrego al SHOPPING CART
		if(!$bExisteItem)
		{
			$strItemIngresado = $CodigoArticulo . "|" . $Imagen . "|" . $Cantidad;
			if(!isset($_SESSION["ShoppingCart"]) || $_SESSION["ShoppingCart"] == "")
				$_SESSION["ShoppingCart"] = $strItemIngresado;
			else
				$_SESSION["ShoppingCart"] = $_SESSION["ShoppingCart"] . ";" . $strItemIngresado;
			
		}
	}
	// FIN DE: PROCEDIMIENTO que AGREGA un ITEM al SHOPPING CART
	
	// PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART
	function RemoveFromShoppingCart($Posicion)
	{
		static $ArrayItems;
		static $ArrayArticulos;
		static $strItemActual;
		static $strItemIngresado;
		static $strShoppingCartResult;
		static $iPosicionActual;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			// Seteo la variable que sera el contenido del SHOPPING CART
			$strShoppingCartResult = "";
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			$iPosicionActual = 1;
			foreach($ArrayItems as $Item)
			{
				if(intval($Posicion) != intval($iPosicionActual) ){
					$strShoppingCartResult = $strShoppingCartResult . $Item . ";";
				}
				$iPosicionActual = $iPosicionActual + 1;
			}
		}
		
		// Cargo los valores ACTUALIZADOS del SHOPPING CART
		if($strShoppingCartResult != ""){
			$_SESSION["ShoppingCart"] = substr($strShoppingCartResult, 0, strlen($strShoppingCartResult)-1);
		}else{
			$_SESSION["ShoppingCart"] = "";
		}
	}
	// FIN DE: PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART

	// PROCEDIMIENTO que QUITA ITEMs al SHOPPING CART
	// Parametro "ListaPosiciones": Lista separada por coma de las posiciones a quitar
	function RemoveItemsFromShoppingCart($ListaPosiciones)
	{
		static $ArrayItems;
		static $ArrayArticulos;
		static $strItemActual;
		static $strItemIngresado;
		static $strShoppingCartResult;
		static $iPosicionActual;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			// Seteo la variable que sera el contenido del SHOPPING CART
			$strShoppingCartResult = "";
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			$ListaPosiciones = "," . $ListaPosiciones . ",";
			
			$iPosicionActual = 1;
			foreach($ArrayItems as $Item)
			{
				if(strrpos($ListaPosiciones, "," . $iPosicionActual . ",") <= 0)
					$strShoppingCartResult = $strShoppingCartResult . $Item . ";";

				$iPosicionActual = intval($iPosicionActual) + 1;
			}
		}
		
		// Cargo los valores ACTUALIZADOS del SHOPPING CART
		if($strShoppingCartResult != "")
			$_SESSION["ShoppingCart"] = substr($strShoppingCartResult, 0, strlen($strShoppingCartResult)-1);
		else
			$_SESSION["ShoppingCart"] = "";
		
	}
	// FIN DE: PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART
	
	// PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART
	function RemoveItemShoppingCart($CodigoArticulo, $Imagen, $Cantidad)
	{
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			static $ArrayItems;
			static $ArrayArticulos;
			static $strItemActual;
			static $strItemIngresado;
			static $strShoppingCartResult;

			// Seteo la variable que sera el contenido del SHOPPING CART
			$strShoppingCartResult = "";
			// Obtengo un Array de los ITEMS del SHOPPING CART
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			// Armo el Item Ingresado
			$strItemIngresado = $CodigoArticulo . "|" . $Imagen . "|" . $Cantidad;
			
			foreach($ArrayItems as $Item)
			{
				// Obtengo un Array de los ARTICULOS de cada ITEM del SHOPPING CART
				$ArrayArticulos = split('\|', $Item);
				// Armo el Item Actual que estoy examinado
				//$strItemActual = $ArrayArticulos[0] . "|" . $ArrayArticulos[1] . "|" . ArrayArticulos[2];
				$strItemActual = $ArrayArticulos[0] . "|" . $ArrayArticulos[1] . "|" . $ArrayArticulos[2];
				// Si el Item Existe, Sumo la cantidad actual con la ingresada
				
				if ( $strItemActual != $strItemIngresado )
					$strShoppingCartResult = $strShoppingCartResult . $strItemActual . ";";
				
			}
			
			if($strShoppingCartResult != ""){
				// Cargo los valores ACTUALIZADOS del SHOPPING CART
				$_SESSION["ShoppingCart"] = substr($strShoppingCartResult, 0, strlen($strShoppingCartResult)-1);
			}else{
				$_SESSION["ShoppingCart"] = "";
			}
		}
	}
	// FIN DE: PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART
	
	function ExistePrdShoppingCart($CodPrd)
	{
		static $bResult;
		$bResult = false;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			static $iIndice;
			static $strItem;
			
			
			for($iIndice=1; $iIndice <= GetCountItemShoppingCart(); $iIndice++)
			{
				$strItem = GetItemShoppingCart($iIndice);				
				if(trim("" . $CodPrd) == trim("" . GetProductoShoppingCart($strItem)))
				{
					$bResult = true;
					break;
				}
			}
			
		}
		
		return $bResult;
	}
	
	// PROCEDIMIENTO que QUITA un ITEM al SHOPPING CART
	function RemoveItemShoppingCart2($Codigo)
	{
		static $ArrayItems;
		static $ArrayPrd;
		static $strItemActual;
		static $strShoppingCartResult;
		
		if(isset($_SESSION["ShoppingCart"]) && $_SESSION["ShoppingCart"] != "")
		{
			$strShoppingCartResult = "";
			$ArrayItems = split(";", $_SESSION["ShoppingCart"]);
			
			if(count($ArrayItems) > 0)
			{
				foreach($ArrayItems as $Item)
				{
					$ArrayPrd = split('\|', $Item);
					$strItemActual = $ArrayPrd[0];
					
					if(trim("".$Codigo) != trim("".$strItemActual))
						$strShoppingCartResult = $strShoppingCartResult . $Item . ";";
				}
				
				if($strShoppingCartResult != "")
				{
					// Cargo los valores ACTUALIZADOS del SHOPPING CART
					$_SESSION["ShoppingCart"] = substr($strShoppingCartResult, 0, strlen($strShoppingCartResult)-1);
				}else{
					$_SESSION["ShoppingCart"] = "";
				}
			}else{
				$ArrayPrd = split('\|', $_SESSION["ShoppingCart"]);
				$strItemActual = $ArrayPrd[0];
				
				if(trim("".$Codigo) == trim("".$strItemActual))
					$_SESSION["ShoppingCart"] = "";
				
			}
		}
	}
?>