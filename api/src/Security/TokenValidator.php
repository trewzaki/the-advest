<?php
namespace Security;

class TokenValidate
{
	public function checkPermission($token)
	{
		
	}

	public function allowGrant($permission, $user)
	{
		$isCheck = false;
		for ($i=0; $i<count($permission); $i++)
		{
			for ($j=0; $j<count($user); $j++)
			{
				if ($permission[$i] == $user[$j])
					return true;
			}
		}
		return false;
	}
}
?>