<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    /**
     * Set the range of rows to read.
     *
     * @param int $startRow
     * @param int $chunkSize
     */
    public function setRows(int $startRow, int $chunkSize): void
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize - 1;
    }

    /**
     * Should this cell be read?
     *
     * @param string $columnAddress
     * @param int $row
     * @param string $worksheetName
     * @return bool
     */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        // Read only rows in our chunk range
        return ($row >= $this->startRow && $row <= $this->endRow);
    }
}
