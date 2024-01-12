<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QuickBookCredentials;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Facades\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->quickbook_credentials = new QuickBookCredentials;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    // Add Customer

    public function add_customer_form()
    {
        $this->updated_access_tokens();
        return view('add_customer_form');
    }

    // View Customer

    public function view_customer_form()
    {         
           
        $dataService = $this->updated_access_token();
        $query = "SELECT * FROM Customer ORDER BY id DESC";
        $customer_details = $dataService->Query($query);    
        return view('view_customer_form',compact('customer_details'));   

        // $query = "SELECT * From Customer";
       
    }

    // Expenses List

    public function viewexpenses_list()
    {         
        return view('expense_list');   
    }

    // Supplier List

    public function supplier_details()
    {         
        return view('supplier_list');   
    }

    // Report List

    public function report_detail()
    {         
        return view('report_list');   
    }


    // Edit Customer

    public function edit_customer_form($id)
    {   
        $dataService = $this->updated_access_token();
    
        // Use parameter $id in the query
        $query = "SELECT * FROM Customer WHERE Id = '$id'";
        //$query = "SELECT * From Customer WHERE id= '73'";
        
        $customer_edit_details = $dataService->Query($query);
    
        return view('edit_customer_form', compact('customer_edit_details'));
    }

    //  Update customer details

    public function update_customer_form(Request $request)
    {
        // return $request->all();

        // Static format
        $dataService = $this->updated_access_token();
        $config = config('quickbooks');
        $dataService = DataService::Configure([
            'auth_mode'         => 'oauth2',
            'ClientID'          => $config['client_id'],
            'ClientSecret'      => $config['client_secret'],
            'RedirectURI'       => $config['redirect_uri'],
            'accessTokenKey'    => $config['access_token'],
            'refreshTokenKey'   => $config['refresh_token'],
            'QBORealmID'        => $config['realm_id'],
            'baseUrl'           => $config['base_url'],
        ]);

        // Dynamic format

        // if($request->company_id==1)
        // {
        //     $config = config('quickbooks');
        //     $dataService = DataService::Configure([
        //         'auth_mode'         => 'oauth2',
        //         'ClientID'          => $config['client_id'],
        //         'ClientSecret'      => $config['client_secret'],
        //         'RedirectURI'       => $config['redirect_uri'],
        //         'accessTokenKey'    => $config['access_token'],
        //         'refreshTokenKey'   => $config['refresh_token'],
        //         'QBORealmID'        => $config['realm_id'],
        //         'baseUrl'           => $config['base_url'],
        //     ]);
        // }
        // else if($request->company_id==2)
        // {
        //     $config = config('quickbooks_2');
        //     $dataService = DataService::Configure([
        //         'auth_mode'         => 'oauth2',
        //         'ClientID'          => $config['client_id'],
        //         'ClientSecret'      => $config['client_secret'],
        //         'RedirectURI'       => $config['redirect_uri'],
        //         'accessTokenKey'    => $config['access_token'],
        //         'refreshTokenKey'   => $config['refresh_token'],
        //         'QBORealmID'        => $config['realm_id'],
        //         'baseUrl'           => $config['base_url'],
        //     ]);
        // }
       
        $displayname = $request->name;

        //  $query = "SELECT * From Customer WHERE DisplayName= '{$displayname}'";
        $query = "SELECT * From Customer WHERE Id= '$request->id'";
        //$query = "SELECT * From Customer";

        $customer = $dataService->Query($query);

        if(isset($customer) && !empty($customer) && count($customer)>0)
        {
            
            $customer= $customer[0];
            $customer->Id = $customer->Id;
            $customer->GivenName = $displayname;
            $customer->FamilyName = $displayname;
            $customer->DisplayName = $displayname;
            $customer->Organization = 'Softheights';
            $customer->CompanyName = 'Softheights';
            $customer->BusinessNumber = '1111111';
            $customer->Mobile = '2222222';
            $customer->AlternatePhone = '3333';
            $customer->OtherContactInfo = '4444';
            $customer->PrimaryPhone->FreeFormNumber = '0123456789';
            // $customer->PrimaryEmailAddr->Address = $request['email'];

            try{

                $result= $dataService->Update($customer);
                echo'Successfully Update';
            }catch (ServiceException $ex){
                echo "Updation Error:".$ex->getMessage();
            }
        }else{
            $customer = Customer::Create([
                "GivenName" =>$displayname,
                "FamilyName"=>$displayname,
                "DisplayName"=>$displayname,
                "PrimaryEmailAddr" =>[
                    "Address" =>$request['email']
                ],
                "BillAddr" =>[
                    "Line1" => "123 Main Street",
                    "Line2" => "Mountain ",
                    "Country" => "USA",
                    "CountrySubDivisionCode" => "CA",
                    "PostalCode" => "94042"
                ],
                "PrimaryPhone" =>[
                    "FreeFormNumber" => '+92303645646'
                ]
            ]);

            try{
              return  $result= $dataService->Add($customer);
                echo'Successfully added11';
                //$this->p($result);
            }catch (ServiceException $ex){
                echo "New Customer Error:".$ex->getMessage();
            }
        }

        $this->p($customer);
    }


    // Delete Customer

    public function delete_customer($id)
    {   
        // $dataService = $this->updated_access_token();
        $config = config('quickbooks');
        $dataService = DataService::Configure([
            'auth_mode'         => 'oauth2',
            'ClientID'          => $config['client_id'],
            'ClientSecret'      => $config['client_secret'],
            'RedirectURI'       => $config['redirect_uri'],
            'accessTokenKey'    => $config['access_token'],
            'refreshTokenKey'   => $config['refresh_token'],
            'QBORealmID'        => $config['realm_id'],
            'baseUrl'           => $config['base_url'],
        ]);

        //  $query = "SELECT * From Customer WHERE DisplayName= '{$displayname}'";
        $query = "SELECT * From Customer WHERE Id= '$id'";
        //$query = "SELECT * From Customer";

        $customer = $dataService->Query($query);

        if(isset($customer) && !empty($customer) && count($customer)>0)
        {            
            $customer= $customer[0];          
            $customer->domain = "QBO";
            $customer->Id = $id;
            $customer->sparse = "true";
            $customer->SyncToken = "0";
            $customer->Active = "false";               

            try{

                $result= $dataService->Update($customer);
                echo'Successfully Update';
            }catch (ServiceException $ex){
                echo "Updation Error:".$ex->getMessage();
            }
        }else{
            $customer = Customer::Create([
                "GivenName" =>$displayname,
                "FamilyName"=>$displayname,
                "DisplayName"=>$displayname,
                "PrimaryEmailAddr" =>[
                    "Address" =>$request['email']
                ],
                "BillAddr" =>[
                    "Line1" => "123 Main Street",
                    "Line2" => "Mountain ",
                    "Country" => "USA",
                    "CountrySubDivisionCode" => "CA",
                    "PostalCode" => "94042"
                ],
                "PrimaryPhone" =>[
                    "FreeFormNumber" => '+92303645646'
                ]
            ]);

            try{
              return  $result= $dataService->Add($customer);
                echo'Successfully added11';
                //$this->p($result);
            }catch (ServiceException $ex){
                echo "New Customer Error:".$ex->getMessage();
            }
        }

        $this->p($customer);
    }


    // Save Customer

    public function savecustomer(Request $request)
    {
      
       
        $dataService = $this->updated_access_token();
    
        
        if($request->company_id==1)
        {
            $config = config('quickbooks');
             // get token from db
            $token_data=QuickBookCredentials::where('client_id',$config['client_id'])->first();  
            $access_token= $token_data['access_token'];
            $refresh_token= $token_data['refresh_token'];

            
            $dataService = DataService::Configure([
                'auth_mode'         => 'oauth2',
                'ClientID'          => $config['client_id'],
                'ClientSecret'      => $config['client_secret'],
                'RedirectURI'       => $config['redirect_uri'],
                'accessTokenKey'    => $access_token,
                'refreshTokenKey'   => $refresh_token,
                'QBORealmID'        => $config['realm_id'],
                'baseUrl'           => $config['base_url'],
            ]);
        }
        else if($request->company_id==2)
        {
            $config = config('quickbooks_2');
            $dataService = DataService::Configure([
                'auth_mode'         => 'oauth2',
                'ClientID'          => $config['client_id'],
                'ClientSecret'      => $config['client_secret'],
                'RedirectURI'       => $config['redirect_uri'],
                'accessTokenKey'    => $config['access_token'],
                'refreshTokenKey'   => $config['refresh_token'],
                'QBORealmID'        => $config['realm_id'],
                'baseUrl'           => $config['base_url'],
            ]);
        }
       
        $displayname = $request['name'];

         $query = "SELECT * From Customer WHERE DisplayName= '{$displayname}'";
        //$query = "SELECT * From Customer WHERE id= '73'";
        //$query = "SELECT * From Customer";

        $customer = $dataService->Query($query);

        if(isset($customer) && !empty($customer) && count($customer)>0)
        {
            
            $customer= $customer[0];
            $customer->Id = $customer->Id;
            $customer->GivenName = $displayname;
            $customer->FamilyName = $displayname;
            $customer->DisplayName = $displayname;
            $customer->Organization = 'Softheights';
            $customer->CompanyName = 'Softheights';
            $customer->BusinessNumber = '1111111';
            $customer->Mobile = '2222222';
            $customer->AlternatePhone = '3333';
            $customer->OtherContactInfo = '4444';
            $customer->PrimaryPhone->FreeFormNumber = '0123456789';
            $customer->PrimaryEmailAddr->Address = $request['email'];

            try{

                $result= $dataService->Update($customer);
                echo'Successfully Update';
            }catch (ServiceException $ex){
                echo "Updation Error:".$ex->getMessage();
            }
        }else{
            $customer = Customer::Create([
                "GivenName" =>$displayname,
                "FamilyName"=>$displayname,
                "DisplayName"=>$displayname,
                "PrimaryEmailAddr" =>[
                    "Address" =>$request['email']
                ],
                "BillAddr" =>[
                    "Line1" => "123 Main Street",
                    "Line2" => "Mountain ",
                    "Country" => "USA",
                    "CountrySubDivisionCode" => "CA",
                    "PostalCode" => "94042"
                ],
                "PrimaryPhone" =>[
                    "FreeFormNumber" => '+92303645646'
                ]
            ]);

            try {
                $result = $dataService->Add($customer);
                echo 'Successfully added';
            } catch (ServiceException $ex) {
                echo "New Customer Error:" . $ex->getMessage();
            }
            
        }

        $this->p($customer);

        //return view('add_customer_form');
    }

    public function updated_access_token()
    {
        $config = config('quickbooks');

        $quickbook_credentials = $this->quickbook_credentials->where('status',0)->first();

        $this->p($quickbook_credentials);     

        // get token from db
         $token_data=QuickBookCredentials::where('client_id',$config['client_id'])->first();  
         $access_token= $token_data['access_token'];
         $refresh_token= $token_data['refresh_token'];

         $dataService = DataService::Configure([
            'auth_mode'                                     => 'oauth2',
            'ClientID'                                      => $config['client_id'],
            'ClientSecret'                                  => $config['client_secret'],
            'RedirectURI'                                   => $config['redirect_uri'],
            'accessTokenKey'                                => $access_token,
            'refreshTokenKey'                               => $refresh_token,
            'QBORealmID'                                    => $config['realm_id'],
            'baseUrl'                                       => $config['base_url'],
            'token_refresh_interval_before_expiry'          => 1800,
        ]);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($config['refresh_token']);

        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        // update token from db
        QuickBookCredentials::where('client_id',$config['client_id'])->update(['access_token'=> $accessTokenValue, 'refresh_token'=> $refreshTokenValue]);  
        
        $result = [
            "token_type"                     => "bearer",
            "expires_in"                     => 3600,
            "refresh_token"                  => $refreshTokenValue,
            "x_refresh_token_expires_in"     => 15551893,
            "access_token"                   => $accessTokenValue,
        ];   
        

        echo "Result Array: <pre>";
        print_r($result);
        echo "</pre>";        
      
        return $dataService;

    }

  

    public function updated_access_tokens()
    {
        $config = config('quickbooks');
        $quickbook_credentials = $this->quickbook_credentials->where('status', 0)->first();
        
        $dataService = DataService::Configure([
            'auth_mode'                                 => 'oauth2',
            'ClientID'                                  => $config['client_id'],
            'ClientSecret'                              => $config['client_secret'],
            'RedirectURI'                               => $config['redirect_uri'],
            'accessTokenKey'                            => $config['access_token'],
            'refreshTokenKey'                           => $config['refresh_token'],
            'QBORealmID'                                => $config['realm_id'],
            'baseUrl'                                   => $config['base_url'],
            'token_refresh_interval_before_expiry'      => 1800,
        ]);
        
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($config['refresh_token']);
        
        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        // Debugging output using echo
        // echo "Access Token: " . $accessTokenValue . "<br>";
        // echo "Refresh Token: " . $refreshTokenValue . "<br>";

        // Debugging output using print_r
        // echo "Config Array: <pre>";
        // print_r($config);
        // echo "</pre>";
        
        // Format the result as desired
        $result = [
            "token_type"                     => "bearer",
            "expires_in"                     => 3600,
            "refresh_token"                  => $refreshTokenValue,
            "x_refresh_token_expires_in"     => 15551893,
            "access_token"                   => $accessTokenValue,
        ];
        
        

        // echo "Result Array: <pre>";
        // print_r($result);
        // echo "</pre>";

       
        
        $envFilePath = base_path('.env');

        // Read the existing contents of the .env file
        $envContent = file_get_contents($envFilePath);

        // Update the values you want in the .env file
        $updatedEnvContent = preg_replace('/QUICKBOOKS_REFRESH_TOKEN_COMPANY1=(.*)/', 'QUICKBOOKS_REFRESH_TOKEN_COMPANY1=' . $refreshTokenValue, $envContent);
        $updatedEnvContent = preg_replace('/QUICKBOOKS_ACCESS_TOKEN_COMPANY1=(.*)/', 'QUICKBOOKS_ACCESS_TOKEN_COMPANY1=' . $accessTokenValue, $updatedEnvContent);

        // Write the updated content back to the .env file
        file_put_contents($envFilePath, $updatedEnvContent);

        // Clear the configuration cache to apply the changes
        Artisan::call('config:clear');

        QuickBookCredentials::where('client_id',$config['client_id'])->update(['access_token'=> $accessTokenValue, 'refresh_token'=> $refreshTokenValue]);

        // Convert the result to JSON and return it
        return json_encode($result);

       
    }

      
    public function p($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
