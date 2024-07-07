import { TextControl, Flex, FlexBlock, FlexItem, Button, Icon, PanelBody, PanelRow } from "@wordpress/components";
import { InspectorControls, BlockControls, AlignmentToolbar, useBlockProps } from "@wordpress/block-editor";
import { ChromePicker } from "react-color";

import "./index.scss";

//IIFE
(function () {
    let locked = false;

    //this function will be called very often
    wp.data.subscribe(function () {
        //blocks with no correctAnswer
        const results = wp.data.select("core/block-editor").getBlocks().filter((block) => {
            return block.name === "ourplugin/are-you-paying-attention"
                && block.attributes.correctAnswer === undefined
        })

        if (results.length && !locked) {
            locked = true;
            wp.data.dispatch("core/editor").lockPostSaving("noanswer");
        }

        if (!results.length && locked) {
            locked = false;
            wp.data.dispatch("core/editor").unlockPostSaving("noanswer");
        }
    })
})();


//wp is a wordpress java libarary
wp.blocks.registerBlockType("ourplugin/are-you-paying-attention",
    {
        title: "Are You Paying Attention",
        icon: "smiley",
        category: "common",
        attributes: {
            question: { type: "string" },
            answers: { type: "array", default: [""] },
            correctAnswer: { type: "number", default: undefined },
            bgColor: { type: "string", default: "#EBEBEB" },
            theAlignment: { type: "string", default: "center" }
        },
        description: "A quiz to test the audience",
        example: {
            attributes: {
                question: "what is frog?",
                answers: ["Mamal", "Reptile", "Fish", "Amphibian"],
                correctAnswer: 3,
                bgColor: "#efefef",
                theAlignment: "center"
            }
        },
        edit: EditComponent,
        save: function (props) {
            return null;
        }
    }
);

function EditComponent(props) {
    //for the block wrapper
    const blockProps = useBlockProps({
        className: "paying-attention-edit-block",
        style: { backgroundColor: props.attributes.bgColor }
    });

    //wp TextControl element passes value directly
    function updateQuestion(value) {
        props.setAttributes({ question: value });
    }

    function deleteAnswer(indexToDelete) {
        const newAnswers = props.attributes.answers.filter((answer, index) => {
            return index != indexToDelete;
        });
        props.setAttributes({ answers: newAnswers });

        if (indexToDelete === props.attributes.correctAnswer) {
            props.setAttributes({ correctAnswer: undefined })
        }
    }

    function markAsCorrect(indexOfCorrect) {
        props.setAttributes({ correctAnswer: indexOfCorrect })
    }

    return (
        <div {...blockProps} >
            <BlockControls>
                <AlignmentToolbar
                    value={props.attributes.theAlignment}
                    onChange={x => props.setAttributes({ theAlignment: x })}
                />
            </BlockControls>
            <InspectorControls>
                <PanelBody title="Background Color" initialOpen={true}>
                    <PanelRow>
                        <ChromePicker
                            color={props.attributes.bgColor}
                            disabledAlpha={true}
                            onChangeComplete={x => props.setAttributes({ bgColor: x.hex })}
                        />
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <TextControl label="Question:" value={props.attributes.question} onChange={updateQuestion} />
            <p>Answers:</p>
            {props.attributes.answers.map((answer, index) => {
                return (
                    <Flex key={index}>
                        <FlexBlock>
                            <TextControl
                                value={answer}
                                autoFocus={answer == undefined}
                                onChange={newValue => {
                                    const newAnswers = props.attributes.answers.concat([]);
                                    newAnswers[index] = newValue;
                                    props.setAttributes({ answers: newAnswers });
                                }}
                            />
                        </FlexBlock>
                        <FlexItem>
                            <Button onClick={() => markAsCorrect(index)}>
                                <Icon className="mark-as-correct"
                                    icon={props.attributes.correctAnswer === index ? "star-filled" : "star-empty"}
                                />
                            </Button>
                        </FlexItem>
                        <FlexItem>
                            <Button isLink className="attention-delete"
                                onClick={() => deleteAnswer(index)}>Delete</Button>
                        </FlexItem>
                    </Flex>
                );
            })}
            <Button isPrimary onClick={() => {
                props.setAttributes({ answers: props.attributes.answers.concat([undefined]) })
            }}>Add another answer</Button>
        </div>
    );
}

