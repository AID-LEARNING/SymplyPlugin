<?php

namespace SenseiTarzan\SymplyPlugin\Task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Block;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Player\BlockBreakRequest;
use SenseiTarzan\SymplyPlugin\Utils\BlockUtils;
use pocketmine\network\mcpe\NetworkSession;
use WeakReference;

class BlockBreakingTask extends Task
{

    /**
     * @param WeakReference<Player> $player
     * @param BlockBreakRequest|null $blockBreakRequest
     */
    private ?BlockBreakRequest $blockBreakRequest = null;
    private int $tickFinish = 1;
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

    public function start(NetworkSession $session, int $tick): void
    {
        $this->setHandler(null);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, 1);
        $this->tickFinish += tick;
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
        if($this->blockBreakRequest->addTick(BlockUtils::getDestroyRate($player, $player->getWorld()->getBlock($origin))) >= $this->tickFinish){
            $player->breakBlock($origin);
            $this->blockBreakRequest = null;
        }
    }
}