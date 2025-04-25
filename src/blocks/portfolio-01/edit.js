import "./editor.scss";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
  const blockProps = useBlockProps();
  return (
    <section {...blockProps}>
      <div className="w-full bg-[#F9F9F9] px-4 py-8 shadow-[inset_0_10px_10px_0px_rgba(0,0,0,0.1)]">
        <h2>Portfolio 01</h2>
      </div>
    </section>
  );
}
