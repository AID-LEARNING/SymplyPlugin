<?php

namespace SenseiTarzan\SymplyPlugin\Task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Block;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Player\BlockBreakRequest;
use SenseiTarzan\SymplyPlugin\Utils\BlockUtils;
use WeakReference;

class BlockBreakingTask extends Task
{

    /**
     * @param WeakReference<Player> $player
     * @param BlockBreakRequest|null $blockBreakRequest
     */
    private ?BlockBreakRequest $blockBreakRequest = null;
    public function __construct(private readonly WeakReference $player )
    {
    }


    /**
     * @return BlockBreakRequest|null
     */
    public function getBlockBreakRequest(): ?BlockBreakRequest
    {
        return $this->blockBreakRequest;
    }

    public function setBlockBreakRequest(?BlockBreakRequest $blockBreakRequest): void {
        $this->blockBreakRequest = $blockBreakRequest;
    }

    public function start(): void
    {
        $this->setHandler(null);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function stop(): void
    {
        $this->getHandler()?->cancel();
    }

    public function onRun(): void
    {
        /**
         * @var Player|null $player
         */
        $player = $this->player->get();
        if (!$player || !$this->blockBreakRequest) {
            $this->stop();
            return;
        }
        $origin = $this->blockBreakRequest->getOrigin();
        if (!$player->getWorld()->isInLoadedTerrain($origin)){
            return;
        }
        $block = $player->getWorld()->getBlock($origin);
        if($this->blockBreakRequest->addTick(BlockUtils::getDestroyRate($player, $block)) >= 1){
            $player->breakBlock($origin);
            $this->blockBreakRequest = null;
        }
    }
}