<?php

namespace Elielelie\Sap\Functions;

use Elielelie\Sap\Connectors\Connection;
use Exception;

class RfcGoodsMovement extends FunctionModule
{
    private string $postingDate;

    private string $documentDate;

    private string $headerText;

    private string $gmCode     = '04';

    private array $items       = [];

    /**
     * Buffer for the current item being built
     */
    private array $currentItem = [];

    /**
     * Create a new instance of RfcGoodsMovement.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'BAPI_GOODSMVT_CREATE');
    }

    /**
     * @return $this
     */
    public function postingDate(string $date): static
    {
        $this->postingDate = $date;

        return $this;
    }

    /**
     * @return $this
     */
    public function documentDate(string $date): static
    {
        $this->documentDate = $date;

        return $this;
    }

    /**
     * @return $this
     */
    public function headerText(string $text): static
    {
        $this->headerText = $text;

        return $this;
    }

    /**
     * @return $this
     */
    public function gmCode(string $code): static
    {
        $this->gmCode = $code;

        return $this;
    }

    /**
     * Set GM_CODE to 01 (Goods receipt for purchase order)
     *
     * @return $this
     */
    public function goodsReceipt(): static
    {
        return $this->gmCode('01');
    }

    /**
     * Set GM_CODE to 02 (Goods receipt for production order)
     *
     * @return $this
     */
    public function goodsIssue(): static
    {
        return $this->gmCode('02');
    }

    /**
     * Set GM_CODE to 04 (Transfer posting)
     *
     * @return $this
     */
    public function transferPosting(): static
    {
        return $this->gmCode('04');
    }

    /**
     * Set item material
     *
     * @return $this
     */
    public function material(string $value): static
    {
        $this->currentItem['MATERIAL'] = strtoupper($value);

        return $this;
    }

    /**
     * Set item plant
     *
     * @return $this
     */
    public function plant(string $value): static
    {
        $this->currentItem['PLANT'] = strtoupper($value);

        return $this;
    }

    /**
     * Set item storage location
     *
     * @return $this
     */
    public function storageLocation(string $value): static
    {
        $this->currentItem['STGE_LOC'] = strtoupper($value);

        return $this;
    }

    /**
     * Set item movement type (e.g., 309, 311, 261, 101)
     *
     * @return $this
     */
    public function movementType(string $value): static
    {
        $this->currentItem['MOVE_TYPE'] = $value;

        return $this;
    }

    /**
     * Set item quantity
     *
     * @param  float|int|string $value
     * @return $this
     */
    public function quantity($value): static
    {
        $this->currentItem['ENTRY_QNT'] = $value;

        return $this;
    }

    /**
     * Set item unit of measure
     *
     * @return $this
     */
    public function unit(string $value): static
    {
        $this->currentItem['ENTRY_UOM'] = strtoupper($value);

        return $this;
    }

    /**
     * Set item batch
     *
     * @return $this
     */
    public function batch(string $value): static
    {
        $this->currentItem['BATCH'] = strtoupper($value);

        return $this;
    }

    /**
     * Set item text
     *
     * @return $this
     */
    public function itemText(string $value): static
    {
        $this->currentItem['ITEM_TEXT'] = $value;

        return $this;
    }

    /**
     * Set production order ID
     *
     * @return $this
     */
    public function order(string $value): static
    {
        $this->currentItem['ORDERID'] = strtoupper($value);

        return $this;
    }

    /**
     * Set cost center
     *
     * @return $this
     */
    public function costCenter(string $value): static
    {
        $this->currentItem['COSTCENTER'] = strtoupper($value);

        return $this;
    }

    /**
     * Set destination material (for 309 transfers)
     *
     * @return $this
     */
    public function toMaterial(string $value): static
    {
        $this->currentItem['MOVE_MAT'] = strtoupper($value);

        return $this;
    }

    /**
     * Set destination plant
     *
     * @return $this
     */
    public function toPlant(string $value): static
    {
        $this->currentItem['MOVE_PLANT'] = strtoupper($value);

        return $this;
    }

    /**
     * Set destination storage location
     *
     * @return $this
     */
    public function toStorageLocation(string $value): static
    {
        $this->currentItem['MOVE_STLOC'] = strtoupper($value);

        return $this;
    }

    /**
     * Set destination batch
     *
     * @return $this
     */
    public function toBatch(string $value): static
    {
        $this->currentItem['MOVE_BATCH'] = strtoupper($value);

        return $this;
    }

    /**
     * Pushes the current item buffer to the items list and clears the buffer.
     *
     * @return $this
     */
    public function pushItem(): static
    {
        if (! empty($this->currentItem)) {
            $this->items[]     = $this->currentItem;
            $this->currentItem = [];
        }

        return $this;
    }

    /**
     * Add a raw item array or push the current item if null.
     *
     * @return $this
     */
    public function addItem(?array $item = null): static
    {
        if ($item) {
            $this->items[] = $item;
        } else {
            $this->pushItem();
        }

        return $this;
    }

    /**
     * Execute the BAPI_GOODSMVT_CREATE with automatic Commit/Rollback.
     * Returns the Material Document Number and Year.
     *
     * @throws Exception
     */
    public function create(): array
    {
        // Auto-push if user forgot
        $this->pushItem();

        $this->param('GOODSMVT_HEADER', [
            'PSTNG_DATE' => $this->postingDate ?? date('Ymd'),
            'DOC_DATE'   => $this->documentDate ?? date('Ymd'),
            'PR_UNAME'   => $_ENV['USERNAME'] ?? '',
            'HEADER_TXT' => $this->headerText ?? '',
        ]);

        $this->param('GOODSMVT_CODE', [
            'GM_CODE' => $this->gmCode,
        ]);

        $this->param('GOODSMVT_ITEM', $this->items);

        $result = $this->execute();

        if ($this->hasErrors($result)) {
            $this->rollback();

            throw new Exception('SAP Error: ' . $this->getFirstErrorMessage($result));
        }

        $this->commit();

        return [
            'document' => $result['MATERIALDOCUMENT'] ?? null,
            'year'     => $result['MATDOCUMENTYEAR'] ?? null,
        ];
    }

    /**
     * Check if the result contains errors (Type E or A)
     */
    private function hasErrors(array $result): bool
    {
        if (! isset($result['RETURN'])) {
            return false;
        }

        foreach ($result['RETURN'] as $msg) {
            if ($msg['TYPE'] === 'E' || $msg['TYPE'] === 'A') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the first error message from the RETURN table
     */
    private function getFirstErrorMessage(array $result): string
    {
        foreach ($result['RETURN'] as $msg) {
            if ($msg['TYPE'] === 'E' || $msg['TYPE'] === 'A') {
                return $msg['MESSAGE'] ?? 'Unknown SAP error';
            }
        }

        return 'Unknown SAP error';
    }

    /**
     * Execute BAPI_TRANSACTION_COMMIT
     */
    private function commit(): void
    {
        $commit = new FunctionModule($this->connection, 'BAPI_TRANSACTION_COMMIT');
        $commit->param('WAIT', 'X');
        $commit->execute();
    }

    /**
     * Execute BAPI_TRANSACTION_ROLLBACK
     */
    private function rollback(): void
    {
        $rollback = new FunctionModule($this->connection, 'BAPI_TRANSACTION_ROLLBACK');
        $rollback->execute();
    }
}
