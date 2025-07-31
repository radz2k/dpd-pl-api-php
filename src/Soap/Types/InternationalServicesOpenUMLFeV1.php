<?php

namespace radz2k\Dpd\Soap\Types;

class InternationalServicesOpenUMLFeV1 {
    
    /**
     * @var ServiceDPDPudoReturnUMLFeV1
     */
    private $pudoReturn;
     
    /**
     * @return ServiceDPDPudoReturnUMLFeV1
     */
    public function getPudoReturn(): ?ServiceDPDPudoReturnUMLFeV1 {
        return $this->pudoReturn;
    }

    /**
     * @param ServiceTiresExportOpenUMLFeV1 $pudoReturn
     * @return InternationalServicesOpenUMLFeV1
     */
    public function setPudoReturn(ServiceDPDPudoReturnUMLFeV1 $pudoReturn): InternationalServicesOpenUMLFeV1 {
        $this->pudoReturn = $pudoReturn;
        return $this;
    }
}
