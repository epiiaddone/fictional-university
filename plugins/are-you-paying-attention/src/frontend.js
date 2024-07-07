import "./frontend.scss";
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

const divsToUpdate = document.querySelectorAll(".paying-attention-update-me");

divsToUpdate.forEach((div) => {
    div.classList.remove("paying-attention-update-me");
    const data = JSON.parse(div.querySelector("pre").innerHTML);
    const root = ReactDOM.createRoot(div);
    root.render(<Quiz {...data} />);
});

function Quiz(props) {
    const [isCorrect, setIsCorrect] = useState(null);

    useEffect(() => {
        if (isCorrect === false) {
            setTimeout(() => setIsCorrect(null), 2600)
        }
    }, [isCorrect])

    function handleAnswer(index) {
        if (index === props.correctAnswer) {
            setIsCorrect(true);
        }
        else { setIsCorrect(false); }
    }

    return (
        <div
            className="paying-attention-frontend"
            style={{ backgroundColor: props.bgColor, textAlign: props.theAlignment }}
        >
            <p>{props.question}</p>
            <ul>
                {props.answers.map((answer, index) => {
                    return (<li
                        className={
                            (isCorrect && index == props.correctAnswer ? "no-click" : "")
                            + (isCorrect && index != props.correctAnswer ? "fade-incorrect" : "")
                        }
                        key={index}
                        onClick={isCorrect ? undefined : () => handleAnswer(index)}
                    >
                        {isCorrect && index === props.correctAnswer && (
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="bi bi-check" viewBox="0 0 16 16">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                            </svg>
                        )}
                        {answer}
                    </li>)
                })}
            </ul>
            <div className={isCorrect ? "correct-message correct-message--visible" : "correct-message"}>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="" className="bi bi-emoji-smile" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                </svg>
                <p>Correct</p>
            </div>
            <div className={isCorrect === false ? "incorrect-message correct-message--visible" : "incorrect-message"}>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="" className="bi bi-emoji-frown" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.5 3.5 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.5 4.5 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                </svg>
                <p>Wrong</p>
            </div>
        </div>
    )
}
