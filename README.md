# SymplyPluin
## Config 
### blockNetworkIdsAreHashes is true

you need to modify in the ChunkSerializer in pmmp

here is the line to modify

```diff
public static function serializeSubChunk(SubChunk $subChunk, BlockTranslator $blockTranslator, PacketSerializer $stream, bool $persistentBlockStates) : void{
                 ...
                 foreach($palette as $p){
-                        $stream->put(Binary::writeUnsignedVarIntVarInt($blockTranslator->internalIdToNetworkId($p) << 1));
+                        $stream->put(Binary::writeVarInt($blockTranslator->internalIdToNetworkId($p)));
                 }
 }
 ```