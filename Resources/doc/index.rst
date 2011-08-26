========
Overview
========

This bundle contains some useful utils to manipulate bioinformatics data (fasta sequences for example).


How does it work?
-----------------

This bundle contains some validator constraints for fasta sequences.
It also add some useful twig extensions.

Installation
------------

Checkout a copy of the bundle code::

    git submodule add gitolite@chili.genouest.org:sf2-bioinfobundle vendor/bundles/Genouest/Bundle/BioinfoBundle
    
Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Genouest\Bundle\BioinfoBundle\GenouestBioinfoBundle(),
        // ...
    );

Make sure that you also register the namespaces with the autoloader::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Genouest\\Bundle' => __DIR__.'/../vendor/bundles',
        // ...
    ));

Configuration
-------------

No specific configuration is needed for this bundle.

Customization
-------------

### Fasta validation

This bundle comes with specific Constraints to validate fasta sequences in a form.
You can use them like this:

    /**
     * @Genouest\Bundle\BioinfoBundle\Constraints\Fasta(seqType = "PROT_OR_ADN")
     */
    public $pastedSeq; // To validate a fasta sequence pasted in a textarea
    
    /**
     * @Genouest\Bundle\BioinfoBundle\Constraints\FastaFile(maxSize = "104857600", seqType = "PROT_OR_ADN")
     */
    public $fileSeq; // To validate a fasta file uploaded

In this example, PROT_OR_ADN can be replaced by ADN, PROT or PROSITE depending on the type of sequence you want to validate.
The Genouest\Bundle\BioinfoBundle\Constraints\FastaFileValidator extends Symfony\Component\Validator\Constraints\FileValidator, so you can use the same
validation options with it (maxSize in this example).

### Twig extensions

The following filters are available with this bundle:

    yourStr|split(',') will split your string with the ',' delimiter
    yourStr|truncate(6, '...') will truncate your string at the 6th character and add the '...' suffix
    yourStr|startsWith('foo') returns true if yourStr starts with 'foo'
    yourStr|endsWith('bar') returns true if yourStr ends with 'bar'

