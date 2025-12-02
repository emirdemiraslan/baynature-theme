import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  SelectControl,
  ToggleControl,
  TextControl,
  RangeControl,
  ColorPalette,
  ComboboxControl,
  Button,
  Popover,
  BaseControl,
} from "@wordpress/components";
import { useState, useMemo } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import { store as coreStore } from "@wordpress/core-data";
import ServerSideRender from "@wordpress/server-side-render";

const InlineColorControl = ({ label, value, onChange, help }) => {
  const [isOpen, setIsOpen] = useState(false);
  return (
    <BaseControl help={help} className="bn-inline-color-control">
      <div
        className="components-base-control__field"
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
          marginBottom: "8px",
        }}
      >
        <label
          className="components-base-control__label"
          style={{ marginBottom: 0 }}
        >
          {label}
        </label>
        <div style={{ display: "flex", alignItems: "center", gap: "4px" }}>
          <Button
            onClick={() => setIsOpen(!isOpen)}
            style={{
              backgroundColor: value,
              width: "24px",
              height: "24px",
              borderRadius: "50%",
              border: "1px solid #ccc",
              padding: 0,
            }}
            className="bn-color-swatch"
          >
            {!value && (
              <span
                className="components-color-palette__custom-color-gradient"
                style={{
                  width: "100%",
                  height: "100%",
                  borderRadius: "50%",
                  background:
                    "conic-gradient(#eee 0.25turn, white 0.25turn 0.5turn, #eee 0.5turn 0.75turn, white 0.75turn)",
                }}
              ></span>
            )}
          </Button>
          {isOpen && (
            <Popover onClose={() => setIsOpen(false)}>
              <div style={{ padding: "16px" }}>
                <ColorPalette
                  value={value}
                  onChange={onChange}
                  clearable={false}
                />
              </div>
            </Popover>
          )}
          <Button
            icon="trash"
            label={__("Clear", "bn-newspack-child")}
            onClick={() => onChange(undefined)}
            disabled={!value}
            isSmall
            style={{ minWidth: "24px", padding: 0 }}
          />
        </div>
      </div>
    </BaseControl>
  );
};

export default function Edit({ attributes, setAttributes }) {
  const {
    postId,
    contentPosition,
    mobileHeight,
    desktopHeight,
    showExcerpt,
    showAuthor,
    showCategory,
    showDate,
    overlay,
    taxonomy,
    featuredImagePosition,
    besideBackgroundColor,
    titleFontSize,
    titleColor,
    titleBackgroundColor,
    categoryFontSize,
    categoryColor,
    categoryBackgroundColor,
    subheadingFontSize,
    subheadingColor,
    subheadingBackgroundColor,
    authorFontSize,
    authorColor,
    authorBackgroundColor,
    dateFontSize,
    dateColor,
    dateBackgroundColor,
    typographyScale,
    imageAlignment,
  } = attributes;

  const [search, setSearch] = useState("");

  const { posts, isResolving } = useSelect(
    (select) => {
      const dataStore = select(coreStore);
      const query = {
        per_page: 20,
        _embed: true,
        status: "publish",
        search: search || undefined,
      };

      const postTypes = ["post", "article", "mec-events"];

      const fetched = postTypes
        .map((type) => {
          const records = dataStore.getEntityRecords("postType", type, query);
          return records || [];
        })
        .flat();

      const resolving = postTypes.some((type) =>
        dataStore.isResolving("getEntityRecords", ["postType", type, query])
      );

      return { posts: fetched, isResolving: resolving };
    },
    [search]
  );

  const postOptions = useMemo(() => {
    const options = posts
      ? posts.map((post) => ({ label: post.title.rendered, value: post.id }))
      : [];

    options.unshift({
      label: __("Latest post (fallback)", "bn-newspack-child"),
      value: 0,
    });

    return options;
  }, [posts]);

  const blockProps = useBlockProps();

  const selectedPost = postOptions.find((option) => option.value === postId);

  return (
    <>
      <InspectorControls>
        <PanelBody title={__("Hero Settings", "bn-newspack-child")}>
          <ComboboxControl
            label={__("Select post", "bn-newspack-child")}
            value={postId}
            options={postOptions}
            onChange={(val) =>
              setAttributes({ postId: Number.parseInt(val, 10) || 0 })
            }
            onFilterValueChange={(value) => setSearch(value)}
            help={__("Type to search posts by title.", "bn-newspack-child")}
          />
          {isResolving && (
            <p className="bn-hero-post__search-status">
              {__("Searching…", "bn-newspack-child")}
            </p>
          )}
          <SelectControl
            label={__("Featured Image Position", "bn-newspack-child")}
            value={featuredImagePosition}
            options={[
              {
                label: __("Behind article title", "bn-newspack-child"),
                value: "behind",
              },
              {
                label: __("Beside article title", "bn-newspack-child"),
                value: "beside",
              },
            ]}
            onChange={(val) => setAttributes({ featuredImagePosition: val })}
          />
          <SelectControl
            label={__("Content Position (Desktop)", "bn-newspack-child")}
            value={contentPosition}
            options={[
              { label: __("Left", "bn-newspack-child"), value: "left" },
              { label: __("Center", "bn-newspack-child"), value: "center" },
              { label: __("Right", "bn-newspack-child"), value: "right" },
            ]}
            onChange={(val) => setAttributes({ contentPosition: val })}
          />
          {featuredImagePosition === "beside" && (
            <SelectControl
              label={__("Image Alignment", "bn-newspack-child")}
              value={imageAlignment}
              options={[
                { label: __("Left", "bn-newspack-child"), value: "left" },
                { label: __("Right", "bn-newspack-child"), value: "right" },
              ]}
              onChange={(val) => setAttributes({ imageAlignment: val })}
            />
          )}
          <TextControl
            label={__("Mobile Height", "bn-newspack-child")}
            value={mobileHeight}
            onChange={(val) => setAttributes({ mobileHeight: val })}
          />
          <TextControl
            label={__("Desktop Height", "bn-newspack-child")}
            value={desktopHeight}
            onChange={(val) => setAttributes({ desktopHeight: val })}
          />
          <ToggleControl
            label={__("Show Sub Heading", "bn-newspack-child")}
            checked={showExcerpt}
            onChange={(val) => setAttributes({ showExcerpt: val })}
          />
          <ToggleControl
            label={__("Show Author", "bn-newspack-child")}
            checked={showAuthor}
            onChange={(val) => setAttributes({ showAuthor: val })}
          />
          <ToggleControl
            label={__("Show Category", "bn-newspack-child")}
            checked={showCategory}
            onChange={(val) => setAttributes({ showCategory: val })}
          />
          <ToggleControl
            label={__("Show Date", "bn-newspack-child")}
            checked={showDate}
            onChange={(val) => setAttributes({ showDate: val })}
          />
          {featuredImagePosition === "beside" && (
            <InlineColorControl
              label={__("Beside layout background color", "bn-newspack-child")}
              value={besideBackgroundColor}
              onChange={(val) => setAttributes({ besideBackgroundColor: val })}
              help={__(
                "CSS color value (e.g. #333333) for the text column.",
                "bn-newspack-child"
              )}
            />
          )}
          <TextControl
            label={__("Overlay Gradient", "bn-newspack-child")}
            value={overlay}
            onChange={(val) => setAttributes({ overlay: val })}
            help={__("CSS gradient string", "bn-newspack-child")}
          />
        </PanelBody>

        <PanelBody title={__("Style", "bn-newspack-child")} initialOpen={false}>
          <RangeControl
            label={__("Typography scale", "bn-newspack-child")}
            value={typographyScale ?? 1}
            onChange={(val) =>
              setAttributes({
                typographyScale: val,
              })
            }
            min={0.3}
            max={2}
            step={0.05}
            help={__(
              "Scales all text sizes in this hero from 0.3× to 2×.",
              "bn-newspack-child"
            )}
          />

          <InlineColorControl
            label={__("Title color", "bn-newspack-child")}
            value={titleColor}
            onChange={(val) => setAttributes({ titleColor: val })}
          />
          <InlineColorControl
            label={__("Title background color", "bn-newspack-child")}
            value={titleBackgroundColor}
            onChange={(val) => setAttributes({ titleBackgroundColor: val })}
          />

          <InlineColorControl
            label={__("Category color", "bn-newspack-child")}
            value={categoryColor}
            onChange={(val) => setAttributes({ categoryColor: val })}
          />
          <InlineColorControl
            label={__("Category background color", "bn-newspack-child")}
            value={categoryBackgroundColor}
            onChange={(val) => setAttributes({ categoryBackgroundColor: val })}
          />

          <InlineColorControl
            label={__("Sub heading color", "bn-newspack-child")}
            value={subheadingColor}
            onChange={(val) => setAttributes({ subheadingColor: val })}
          />
          <InlineColorControl
            label={__("Sub heading background color", "bn-newspack-child")}
            value={subheadingBackgroundColor}
            onChange={(val) =>
              setAttributes({ subheadingBackgroundColor: val })
            }
          />

          <InlineColorControl
            label={__("Author color", "bn-newspack-child")}
            value={authorColor}
            onChange={(val) => setAttributes({ authorColor: val })}
          />
          <InlineColorControl
            label={__("Author background color", "bn-newspack-child")}
            value={authorBackgroundColor}
            onChange={(val) => setAttributes({ authorBackgroundColor: val })}
          />

          <InlineColorControl
            label={__("Date color", "bn-newspack-child")}
            value={dateColor}
            onChange={(val) => setAttributes({ dateColor: val })}
          />
          <InlineColorControl
            label={__("Date background color", "bn-newspack-child")}
            value={dateBackgroundColor}
            onChange={(val) => setAttributes({ dateBackgroundColor: val })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <ServerSideRender block="bn/hero-header" attributes={attributes} />
      </div>
    </>
  );
}
