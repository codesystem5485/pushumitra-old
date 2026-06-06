<?php
use App\Http\Controllers\Api\CommonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnimalsaleController;
use App\Http\Controllers\Api\PaymentController; 
use App\Http\Controllers\Api\BreederController;
use App\Http\Controllers\Api\PashumitraController;
use App\Http\Controllers\Api\RegisteredvetController;
use App\Http\Controllers\Api\AddanimalController;
use App\Http\Controllers\Api\RxreminderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChemistController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\RatingsController;
use App\Http\Controllers\Api\TransporterController;
use App\Http\Controllers\Api\ProductsaleController; 
use App\Http\Controllers\Api\VetHospitalsController;
use App\Http\Controllers\Api\SuppliersController;
use App\Http\Controllers\Api\TrainingCentersController;
use App\Http\Controllers\Api\ShopsController;
use App\Http\Controllers\Api\FarmsController;
use App\Http\Controllers\Api\InstitutionsController;
use App\Http\Controllers\Api\DogShelterController;
use App\Http\Controllers\Api\MilkCollectionController;
use App\Http\Controllers\Api\PoultryHatcheryController;
use App\Http\Controllers\Api\PanjarpolController;
use App\Http\Controllers\Api\EasycareController;
use App\Http\Controllers\Api\LabsController;
use App\Http\Controllers\Api\NgoController;
use App\Http\Controllers\Api\FrontPagesController;
use App\Http\Controllers\Api\UserTransactionsController;
use App\Http\Controllers\Api\ReferenceController;


Route::group(['middleware' => ['cors']], function () {
	 
	Route::get('/get-species', [CommonController::class,'getSpecies'])->middleware('localization'); 
	Route::get('/get-breeds', [CommonController::class,'getBreeds']);
	Route::get('/get-library', [AuthController::class,'getLibrary']);
	Route::get('/get-subcategories', [CommonController::class,'getSubCategories'])->middleware('localization');
	Route::get('/get-categories', [CommonController::class,'getParentCategories']);
	Route::get('/get-grfiles', [CommonController::class,'getGrFiles']);
	Route::get('/get-gresolutionfiles', [CommonController::class,'getGResolutionFiles']);
	Route::get('/get-csractivities', [FrontPagesController::class,'csrActivities']);
	Route::get('/get-csractivity-detail', [FrontPagesController::class,'csrActivityDetails']);
	Route::get('/get-testimonials', [FrontPagesController::class,'testimonials']);
	
	 Route::group(['middleware' => ['api-token']], function () {
		 
	Route::post('/delete-image', [CommonController::class,'deleteImagesUsingModuleId']);
	
	
	Route::get('/get-transactions', [UserTransactionsController::class,'getTransactions']);
	
	/* Animal sale*/
	Route::post('/add-animalsale', [AnimalsaleController::class,'addAnimalForSale']);
	Route::post('/update-animalsale', [AnimalsaleController::class,'updateAnimalForSale']);
	Route::post('/delete-animalsale', [AnimalsaleController::class,'deleteAnimalForSale']);

	/* Breeder */
	Route::post('/add-breeder', [BreederController::class,'addBreeder']);
	Route::post('/update-breeder', [BreederController::class,'updateBreeder']);
	Route::post('/delete-breeder', [BreederController::class,'deleteBreeder']);
	
	/* Add animal*/
	Route::post('/add-animal', [AddanimalController::class,'addAnimal']); 
	Route::post('/update-animal', [AddanimalController::class,'updateAnimal']); 
	Route::post('/delete-animal', [AddanimalController::class,'deleteAnimal']); 
	
	/* Add chemist*/
	Route::post('/add-chemist', [ChemistController::class,'addChemist']); 
	Route::post('/update-chemist', [ChemistController::class,'updateChemist']);
	Route::post('/delete-chemist', [ChemistController::class,'deleteChemist']);
	
	/* Rxreminder */
	Route::post('/add-rxreminder', [RxreminderController::class,'addRxreminder']);
	Route::get('/get-user-info', [RxreminderController::class,'getUserInfoUsingMobile']);
	Route::get('/get-animalowner-list', [RxreminderController::class,'getAnimalOwnerList']);
	Route::get('/get-animal-list', [RxreminderController::class,'getAnimalNameList']);
	Route::get('/get-rxreminder-history', [RxreminderController::class,'getRxReminderHistory']);
	Route::get('/get-rxreminder-history-details', [RxreminderController::class,'getRxReminderHistoryDetails']);
	
	/* payment */
	Route::post('/get-orderid', [PaymentController::class,'generatePaymentOrderId']);
	Route::post('/create-payment', [PaymentController::class,'addPayments']);
 	Route::post('/create-renewal-payment', [PaymentController::class,'addRenewPayments']);
	
	Route::get('/get-config', [PaymentController::class,'getConfig'])->middleware('localization');
	
	Route::get('/get-notifications', [NotificationController::class,'getNotificationList']); 
	Route::get('/get-notification-details', [NotificationController::class,'getNotificationDetails']); 
	
	Route::get('/setting', [CommonController::class,'getSetting']);
	
	/* Ratings Details */
	Route::post('/add-ratings', [RatingsController::class,'addRatings']);
	/* Add Transporter */
	Route::post('/add-transporter', [TransporterController::class,'addTransporter']);
	Route::post('/update-transporter', [TransporterController::class,'updateTransporter']);
	Route::post('/delete-transporter', [TransporterController::class,'deleteTransporter']);
	/* Add Product sale */
	Route::post('/add-productsale', [ProductsaleController::class,'addProductSale']);
	Route::post('/update-productsale', [ProductsaleController::class,'updateProductSale']);
	Route::post('/delete-productsale', [ProductsaleController::class,'deleteProductsale']);
	/* Add Hospital */
	Route::post('/add-hospital', [VetHospitalsController::class,'addHospital']);
	Route::post('/update-hospital', [VetHospitalsController::class,'updateHospital']);
	Route::post('/delete-hospital', [VetHospitalsController::class,'deleteHospital']);
	/* Add Supplier */
	Route::post('/add-supplier', [SuppliersController::class,'addSupplier']);
	Route::post('/update-supplier', [SuppliersController::class,'updateSupplier']);
	Route::post('/delete-supplier', [SuppliersController::class,'deleteSupplier']);
	/* Add Training center */
	Route::post('/add-training-center', [TrainingCentersController::class,'addTrainingCenter']);
	Route::post('/update-training-center', [TrainingCentersController::class,'updateTrainingCenter']);
	Route::post('/delete-training-center', [TrainingCentersController::class,'deleteTrainingCenter']);
	/* Add Shops */
	Route::post('/add-shop', [ShopsController::class,'addShop']);
	Route::post('/update-shop', [ShopsController::class,'updateShop']);
	Route::post('/delete-shop', [ShopsController::class,'deleteShop']);
	/* Add Farms */
	Route::post('/add-farm', [FarmsController::class,'addFarm']);
	Route::post('/update-farm', [FarmsController::class,'updateFarm']);
	Route::post('/delete-farm', [FarmsController::class,'deleteFarm']);
	/* Add institutions */
	Route::post('/add-institution', [InstitutionsController::class,'addInstitution']);
	Route::post('/update-institution', [InstitutionsController::class,'updateInstitution']);
	Route::post('/delete-institution', [InstitutionsController::class,'deleteInstitution']);
	/* Add dogshelter */
	Route::post('/add-dogshelter', [DogShelterController::class,'addDogshelter']);
	Route::post('/update-dogshelter', [DogShelterController::class,'updateDogshelter']);
	Route::post('/delete-dogshelter', [DogShelterController::class,'deleteDogshelter']);
	/* Add milk collection */
	Route::post('/add-milkcollection', [MilkCollectionController::class,'addMilkcollection']);
	Route::post('/update-milkcollection', [MilkCollectionController::class,'updateMilkcollection']);
	Route::post('/delete-milkcollection', [MilkCollectionController::class,'deleteMilkcollection']);
	/* Add poultryhatchery */
	Route::post('/add-poultryhatchery', [PoultryHatcheryController::class,'addPoultryhatchery']);
	Route::post('/update-poultryhatchery', [PoultryHatcheryController::class,'updatePoultryhatchery']);
	Route::post('/delete-poultryhatchery', [PoultryHatcheryController::class,'deletePoultryhatchery']);
	
	/* Add panjarpol */
	Route::post('/add-panjarpol', [PanjarpolController::class,'addPanjarpol']);
	Route::post('/update-panjarpol', [PanjarpolController::class,'updatePanjarpol']); 
	Route::post('/delete-panjarpol', [PanjarpolController::class,'deletePanjarpol']);
	/* Add easycare */
	Route::post('/add-easycare', [EasycareController::class,'addEasycare']);
	Route::post('/update-easycare', [EasycareController::class,'updateEasycare']);
	Route::post('/delete-easycare', [EasycareController::class,'deleteEasycare']);
	/* Add lab */
	Route::post('/add-lab', [LabsController::class,'addLab']);
	Route::post('/update-lab', [LabsController::class,'updateLab']);
	Route::post('/delete-lab', [LabsController::class,'deleteLab']);
	/* Add NGO */
	Route::post('/add-ngo', [NgoController::class,'addNgo']);
	Route::post('/update-ngo', [NgoController::class,'updateNgo']);
	Route::post('/delete-ngo', [NgoController::class,'deleteNgo']);
	
	/* easycare Ratings Details */
	Route::post('/add-easycare-ratings', [RatingsController::class,'addEasycareRatings']);
	Route::get('/update-modules-count', [PaymentController::class,'updateModuleCount']);
	
	});
	
	Route::get('/get-average-rating', [RatingsController::class,'getAverageRatings']);
	Route::get('/get-rating-details', [RatingsController::class,'showAllRatings']);
	Route::get('/get-easycare-average-rating', [RatingsController::class,'getEasycareAverageRatings']);
	Route::get('/get-easycare-rating-details', [RatingsController::class,'showEasycareAllRatings']);
	
	Route::get('/breeder-list', [BreederController::class,'getBreederList']);
	Route::get('/breeder-detail', [BreederController::class,'breederDetail']);
	
	Route::get('/animalsale-list', [AnimalsaleController::class,'getAnimalSaleList']);
	Route::get('/animalsale-detail', [AnimalsaleController::class,'animalSaleDetail']); 
	
	Route::get('/chemist-list', [ChemistController::class,'getChemistList']);
	Route::get('/chemist-detail', [ChemistController::class,'chemistDetail']);
	
	Route::get('/animal-list', [AddanimalController::class,'getAnimalList']);
	Route::get('/animal-detail', [AddanimalController::class,'animalDetail']);
	
	/* Nearest Pashumitra*/
	Route::get('/nearest-pashumitra', [PashumitraController::class,'nearestPashumitraList']);
	Route::get('/pashumitra-detail', [PashumitraController::class,'pashumitraDetail']);
	
	/* Nearest registeredvet*/
	Route::get('/nearest-registeredvet', [RegisteredvetController::class,'nearestRegisteredVetList']);
	Route::get('/registeredvet-detail', [RegisteredvetController::class,'registeredVetDetail']);
	
	/* Search  */
	Route::get('/search-registeredvet', [SearchController::class,'searchRegisteredVetDetails']);
	Route::get('/search-animalsale', [SearchController::class,'searchAnimalForSalesDetails']);
	Route::get('/search-breeder', [SearchController::class,'searchBreederDetails']);
	Route::get('/search-library', [SearchController::class,'searchLibraryDetails']);
	Route::get('/search-chemist', [SearchController::class,'searchChemistDetails']);
	Route::get('/search-vethospitals', [SearchController::class,'searchVethospitals']);

	/* advertisement Details */
	Route::get('/advertisement-list', [AdvertisementController::class,'getAdvertisementList']);
	Route::get('/advertisement-detail', [AdvertisementController::class,'advertisementDetail']);
	
	Route::get('/transporter-list', [TransporterController::class,'getTransporterList']);
	Route::get('/transporter-detail', [TransporterController::class,'transporterDetail']);
	
	Route::get('/productsale-list', [ProductsaleController::class,'getProductsaleList']);
	Route::get('/productsale-detail', [ProductsaleController::class,'productsaleDetail']);
	
	Route::get('/hospitals-list', [VetHospitalsController::class,'getHospitalList']);
	Route::get('/hospital-detail', [VetHospitalsController::class,'hospitalDetail']); 
	
	Route::get('/suppliers-list', [SuppliersController::class,'getSupplierList']);
	Route::get('/supplier-detail', [SuppliersController::class,'supplierDetail']);
	Route::get('/training-centers-list', [TrainingCentersController::class,'getTrainingCenterList']);
	Route::get('/training-center-detail', [TrainingCentersController::class,'trainingCenterDetail']);
	Route::get('/farms-list', [FarmsController::class,'getFarmList']);
	Route::get('/farm-detail', [FarmsController::class,'farmDetail']);
	Route::get('/shops-list', [ShopsController::class,'getShopsList']);
	Route::get('/shop-detail', [ShopsController::class,'shopDetail']);
	Route::get('/institutions-list', [InstitutionsController::class,'getInstitutionList']);
	Route::get('/institution-detail', [InstitutionsController::class,'institutionDetail']);
	Route::get('/dogshelters-list', [DogShelterController::class,'getDogshelterList']);
	Route::get('/dogshelter-detail', [DogShelterController::class,'dogshelterDetail']);
	Route::get('/milkcollections-list', [MilkCollectionController::class,'getMilkcollectionList']);
	Route::get('/milkcollection-detail', [MilkCollectionController::class,'milkcollectionDetail']);
	Route::get('/poultryhatchery-list', [PoultryHatcheryController::class,'getPoultryhatcheryList']);
	Route::get('/poultryhatchery-detail', [PoultryHatcheryController::class,'poultryhatcheryDetail']);
	Route::get('/panjarpol-list', [PanjarpolController::class,'getPanjarpolList']);
	Route::get('/panjarpol-detail', [PanjarpolController::class,'panjarpolDetail']);
	Route::get('/easycare-list', [EasycareController::class,'getEasycareList']);
	Route::get('/easycare-detail', [EasycareController::class,'easycaresDetail']);
	Route::get('/lab-list', [LabsController::class,'getLabList']);
	Route::get('/lab-detail', [LabsController::class,'labDetail']);
	Route::get('/ngo-list', [NgoController::class,'getNgoList']);
	Route::get('/ngo-detail', [NgoController::class,'ngoDetail']);
	
	/* References */
	Route::post('/add-reference', [ReferenceController::class,'addReference']);
	Route::post('/update-reference', [ReferenceController::class,'updateReference']);
	Route::post('/delete-reference', [ReferenceController::class,'deleteReference']);
	Route::post('/add-user-reference', [ReferenceController::class,'addUserReference']);
	Route::get('/user-references', [ReferenceController::class,'getUserReferences']);
	
	Route::get('/references-list', [ReferenceController::class,'getReferenceList']);
	Route::get('/reference-detail', [ReferenceController::class,'referenceDetail']);
	
}); 