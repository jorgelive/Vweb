Symfony2 Excel bundle
============

## Installation

**1**  Add to composer.json to the `require` key

``` yml
    "require" : {
        "gopro/excelbundle": "~2.0",
    }
``` 

**2** Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Gopro\ExcelBundle\GoproExcelBundle(),
    );
```

## TL;DR

- Create an empty object:

``` php
$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
```

- Create an object from a file:

``` php
$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject('file.xls');
```

- Create a Excel5 and write to a file given the object:

```php
$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
$writer->save('file.xls');
```

- Create a Excel5 and create a StreamedResponse:

```php
$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
$response = $writer->createStreamedResponse($writer);
```
## Not Only 'Excel5'

The list of the types are:

1.  'Excel5'
2.  'Excel2007'
3.  'Excel2003XML'
4.  'OOCalc'
5.  'SYLK'
6.  'Gnumeric'
7.  'HTML'
8.  'CSV'

## Example

### Fake Controller

The best place to start is the fake Controller at `Tests/app/Controller/FakeController.php`, that is a working example.

### More example

You could find a lot of examples in the official PHPExcel repository https://github.com/PHPOffice/PHPExcel/tree/develop/Examples

### For lazy devs

``` php
namespace YOURNAME\YOURBUNDLE\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction($name)
    {
        // ask the service for a Excel5
       $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

       $phpExcelObject->getProperties()->setCreator("gopro")
           ->setLastModifiedBy("Jorge Gomez")
           ->setTitle("Office 2005 XLSX Test Document")
           ->setSubject("Office 2005 XLSX Test Document")
           ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
           ->setKeywords("office 2005 openxml php")
           ->setCategory("Test result file");
       $phpExcelObject->setActiveSheetIndex(0)
           ->setCellValue('A1', 'Hello')
           ->setCellValue('B2', 'world!');
       $phpExcelObject->getActiveSheet()->setTitle('Simple');
       // Set active sheet index to the first sheet, so Excel opens this as the first sheet
       $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=stream-file.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;        
    }
}
```

