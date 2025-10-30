# SymplyPlugin

A PocketMine-MP plugin for creating custom items and blocks easily.

## 📚 Documentation

- **[Documentation en Français](DOCUMENTATION.md)** 🇫🇷
- **[English Documentation](DOCUMENTATION_EN.md)** 🇬🇧

Complete guides for creating:
- Custom Items (simple items, food, tools, armor)
- Custom Blocks (simple blocks, blocks with permutations)
- Full examples and code snippets

## 🌐 DeepWiki
https://deepwiki.com/AID-LEARNING/SymplyPlugin

## Config 
### blockNetworkIdsAreHashes is true
this is obsolete
<p/>
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
