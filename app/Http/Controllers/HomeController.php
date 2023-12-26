<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QuickBookCredentials;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Facades\Customer;
use Illuminate\Http\Request;

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

    public function add_customer_form()
    {
        return view('add_customer_form');
    }

    public function savecustomer(Request $request)
    {

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

        $displayname = $request['name'];

        $query = "SELECT * From Customer WHERE DisplayName= '{$displayname}'";

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

            try{
                $result= $dataService->Add($customer);
                echo'Successfully added';
                //$this->p($result);
            }catch (ServiceException $ex){
                echo "New Customer Error:".$ex->getMessage();
            }
        }

        $this->p($customer);
        // $this->p($request->all());

        return view('add_customer_form');
    }

    public function updated_access_token()
    {
        $config = config('quickbooks');

        $quickbook_credentials = $this->quickbook_credentials->where('status',0)->first();

        $this->p($quickbook_credentials);
    
        $dataService = DataService::Configure([
            'auth_mode'                                     => 'oauth2',
            'ClientID'                                      => $config['client_id'],
            'ClientSecret'                                  => $config['client_secret'],
            'RedirectURI'                                   => $config['redirect_uri'],
            'accessTokenKey'                                => $config['access_token'],
            'refreshTokenKey'                               => $config['refresh_token'],
            'QBORealmID'                                    => $config['realm_id'],
            'baseUrl'                                       => $config['base_url'],
            'token_refresh_interval_before_expiry'          => 1800,
        ]);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($config['refresh_token']);

        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        return $dataService;

    }

    public function p($var)
    {

        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
