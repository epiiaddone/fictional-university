import {
    RichText,
    BlockControls,
    InspectorControls,
    __experimentalLinkControl as LinkControl, //alias
    getColorObjectByColorValue //get color name from hex
} from "@wordpress/block-editor"
import {
    ToolBarGroup,
    ToolBarButton,
    Button,
    PanelBody,
    PanelRow,
    ColorPalette,
    Popover//for popup elements that you can open and close with button click
} from "@wordpress/components"
import { link } from "@wordpress/icons"
import { useState } from "@wordpress/element"


wp.blocks.registerBlockType("ourblocktheme/genericbutton", {
    title: 'Generic Button',
    attributes: {
        text: { type: "string" },
        size: { type: "string", default: "large" },
        linkObject: { type: "object", default: { url: "" } },// will store things like id, url
        colorName: { type: "string", default: "blue" }
    },
    edit: EditComponent,
    save: SaveComponent
});

function EditComponent(props) {
    const [isLinkPickerVisbible, setIsLinkPickerVisible] = useState(false)

    function handleTextChange(newSize) {
        props.setAttributes({ text: newSize })
    }

    function buttonHandler() {
        setIsLinkPickerVisible(prev => !prev)
    }

    function handleLinkChange(newLink) {
        props.setAttributes({ linkObject: newLink })
    }

    const ourColors = [
        { name: "blue", color: "#0d3b66" },
        { name: "orange", color: "#ee964b" },
        { name: "dark-orange", color: "#f95738" }
    ]

    const currentColorValue = ourColors.filter(
        (color) => { return color.name === props.attributes.colorName })[0].color

    function handleColorChange(colorCode) {

        //find color name from hex. The hex is needed by wp for the display
        const { name } = getColorObjectByColorValue(ourColors, colorCode)

        props.setAttributes({ colorName: name })
    }

    return (
        <>
            <BlockControls>
                <ToolBarGroup>
                    <ToolBarButton onClick={buttonHandler} icon={link} />
                </ToolBarGroup>
                <ToolBarGroup>
                    <ToolBarButton isPressed={props.attributes.size === "large"} onClick={props.setAttributes({ size: "large" })}>Large</ToolBarButton>
                    <ToolBarButton isPressed={props.attributes.size === "medium"} onClick={props.setAttributes({ size: "medium" })}>Medium</ToolBarButton>
                    <ToolBarButton isPressed={props.attributes.size === "small"} onClick={props.setAttributes({ size: "small" })}>Small</ToolBarButton>
                </ToolBarGroup>
            </BlockControls>
            <InspectorControls>
                <PanelBody title="color" initialOpen={true}>
                    <PanelRow>
                        <ColorPalette colors={ourColors}
                            value={currentColorValue}
                            onChange={handleColorChange} />
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <RichText
                allowedFormats={[]} //restrict the default wp formating
                tagName="a"
                className={`btn btn--${props.attributes.size} btn--${props.attributes.colorName}`}
                value={props.attributes.text}
                onChange={handleTextChange}
            />
            {isLinkPickerVisbible && (
                <Popover position="middle center" onFocusOutside={() => setIsLinkPickerVisible(false)}>
                    <LinkControl
                        settings={[]}
                        value={props.attributes.linkObject}
                        onChange={handleLinkChange}
                    />
                    <Button
                        variant="primary"
                        onClick={() => setIsLinkPickerVisible(false)}
                        style={{ display: "block", width: "100%" }}
                    >Confirm Link
                    </Button>
                </Popover>
            )}
        </>
    )
}


function SaveComponent(props) {

    return (
        <a href={props.attributes.linkObject.url} className={`btn btn--${props.attributes.size} btn--${props.attributes.colorName}`}>
            {props.attributes.text}
        </a>
    )
}